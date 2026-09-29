<?php

class HomeController extends Controller {
    private $settings;

    public function __construct() {
        $this->settings = Setting::getAll();
    }

    public function index() {
        $products = Product::getAll();
        $categories = Category::getAll();
        $allBlogs = Blog::getSummaries();
        $recentOrders = RecentOrder::getAll();
        $settings = $this->settings;

        $systemStats = Cache::remember('home.stats', 60, function (): array {
            $db = Database::getInstance();
            $row = $db->query("SELECT
                (SELECT COUNT(*) FROM orders WHERE status IN ('completed', 'processing')) AS completed_orders,
                (SELECT COUNT(*) FROM users WHERE status = 'active') AS total_users,
                (SELECT AVG(rating) FROM reviews) AS average_rating,
                (SELECT COUNT(*) FROM reviews) AS total_reviews,
                (SELECT COUNT(*) FROM reviews WHERE rating = 5) AS rating_5,
                (SELECT COUNT(*) FROM reviews WHERE rating = 4) AS rating_4,
                (SELECT COUNT(*) FROM reviews WHERE rating BETWEEN 1 AND 3) AS rating_1_3")->fetch() ?: [];
            $total = (int) ($row['total_reviews'] ?? 0);

            return [
                'completed_orders' => (int) ($row['completed_orders'] ?? 0),
                'total_users'      => (int) ($row['total_users'] ?? 0),
                'average_rating'   => isset($row['average_rating']) ? round((float) $row['average_rating'], 1) : 5.0,
                'pct_5'            => $total > 0 ? (int) round(((int) $row['rating_5'] / $total) * 100) : 100,
                'pct_4'            => $total > 0 ? (int) round(((int) $row['rating_4'] / $total) * 100) : 0,
                'pct_1_3'          => $total > 0 ? (int) round(((int) $row['rating_1_3'] / $total) * 100) : 0,
            ];
        });

        // Fetch latest reviews
        $recentReviews = Review::getRecentReviews(6);

        // Detect if mobile browser
        $isMobile = false;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent)) {
            $isMobile = true;
        }

        // Current active tab: default is home
        $tab = $_GET['tab'] ?? 'home';
        if (!in_array($tab, ['home', 'products', 'blog'])) {
            $tab = 'home';
        }

        // Look up active category by slug or seo_slug
        $categorySlug = $_GET['category'] ?? '';
        $activeCategory = null;
        if ($categorySlug !== '' && $categorySlug !== 'all') {
            foreach ($categories as $cat) {
                if (($cat['slug'] ?? '') === $categorySlug || ($cat['seo_slug'] ?? '') === $categorySlug) {
                    $activeCategory = $cat;
                    break;
                }
            }
        }

        // Filtering by category (only applies to products tab)
        if ($activeCategory) {
            $filterSlug = $activeCategory['slug'] ?? '';
            $products = array_filter($products, function($p) use ($filterSlug) {
                return ($p['category_slug'] ?? '') === $filterSlug;
            });
        } elseif ($categorySlug !== '' && $categorySlug !== 'all') {
            $products = array_filter($products, function($p) use ($categorySlug) {
                return ($p['category_slug'] ?? '') === $categorySlug;
            });
        }

        // Filtering by search query
        $q = trim($_GET['q'] ?? '');
        if ($q !== '') {
            // Redirect dynamic search queries to static SEO URLs (Item 1)
            $action = $_GET['action'] ?? 'index';
            if ($action !== 'searchKeyword') {
                $slug = $this->resolveKeywordSlug(Seo::slugify($q));
                header('Location: ' . Url::search($slug), true, 302);
                exit;
            }

            $variants = $this->expandQuery($q);
            $scoredProducts = [];
            foreach ($products as $p) {
                $score = $this->scoreProduct($p, $variants);
                if ($score >= 0.45) {
                    $scoredProducts[] = [
                        'product' => $p,
                        'score' => $score
                    ];
                }
            }
            // Sort by score desc
            usort($scoredProducts, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });
            $products = array_column($scoredProducts, 'product');
        }

        // Sorting logic
        $sort = $_GET['sort'] ?? 'newest';
        if ($q === '') {
            usort($products, function($a, $b) use ($sort) {
                if ($sort === 'price_asc') {
                    return $a['price'] <=> $b['price'];
                } elseif ($sort === 'best_seller') {
                    return $b['sold_count'] <=> $a['sold_count'];
                } else {
                    return strtotime($b['created_at']) <=> strtotime($a['created_at']);
                }
            });
        } else {
            if ($sort === 'price_asc' || $sort === 'best_seller') {
                usort($products, function($a, $b) use ($sort) {
                    if ($sort === 'price_asc') {
                        return $a['price'] <=> $b['price'];
                    } else {
                        return $b['sold_count'] <=> $a['sold_count'];
                    }
                });
            }
        }

        // Blog pagination calculation: 1 row = 3 articles, 2 rows = 6 articles per page
        $blogLimit = 6;
        $totalBlogs = count($allBlogs);
        $totalBlogPages = max(1, (int) ceil($totalBlogs / $blogLimit));

        if ($tab === 'home') {
            // No pagination slicing for home tab (uses JS "Xem thêm")
            $page = 1;
            $totalPages = 1;
            $blogPage = 1;
            $blogs = array_slice($allBlogs, 0, 3);
        } elseif ($tab === 'blog') {
            $page = 1;
            $totalPages = 1;

            $blogPage = max(1, (int) ($_GET['page'] ?? 1));
            if ($blogPage > $totalBlogPages && $totalBlogPages > 0) {
                $blogPage = $totalBlogPages;
            }
            $blogOffset = ($blogPage - 1) * $blogLimit;
            $blogs = array_slice($allBlogs, $blogOffset, $blogLimit);
        } else {
            // Standard pagination for products tab: 3 rows of 4 products = 12
            $page = max(1, (int) ($_GET['page'] ?? 1));
            $limit = 12;
            $offset = ($page - 1) * $limit;

            $totalFilteredProducts = count($products);
            $totalPages = max(1, ceil($totalFilteredProducts / $limit));
            $products = array_slice($products, $offset, $limit);

            $blogPage = 1;
            $blogs = array_slice($allBlogs, 0, 6);
        }

        $action = $_GET['action'] ?? 'index';
        if ($action === 'searchKeyword') {
            $keyword = $_GET['keyword'] ?? '';
            $seoData = $this->getKeywordSeoData($keyword);
            $canonical = Url::search($keyword) . ($page > 1 ? '?page=' . $page : '');
            $prevUrl = ($page > 1) ? Url::search($keyword) . ($page > 2 ? '?page=' . ($page - 1) : '') : null;
            $nextUrl = ($page < $totalPages) ? Url::search($keyword) . '?page=' . ($page + 1) : null;
            Seo::set([
                'title'       => $seoData['title'],
                'description' => $seoData['description'],
                'keywords'    => $seoData['keywords'],
                'image'       => url('assets/images/gemini_share.webp'),
                'canonical'   => $canonical,
                'type'        => 'website',
                'robots'      => 'index,follow',
                'prev'        => $prevUrl,
                'next'        => $nextUrl,
                'structured'  => $this->productItemListSchema($products, $canonical),
            ]);
        } elseif ($activeCategory) {
            $catName = $activeCategory['name'] ?? '';
            $seoTitle = !empty($activeCategory['seo_title']) ? $activeCategory['seo_title'] : ($catName . ' - Mua tài khoản ' . $catName . ' giá rẻ');
            $seoDesc  = !empty($activeCategory['seo_description']) ? $activeCategory['seo_description'] : ('Cung cấp tài khoản ' . $catName . ' chính chủ giá rẻ nhất thị trường. Hỗ trợ kích hoạt tự động nhanh chóng, bảo hành 1 đổi 1 uy tín.');
            $seoKey   = !empty($activeCategory['seo_keywords']) ? explode(',', $activeCategory['seo_keywords']) : ['mua tài khoản ' . mb_strtolower($catName), 'tài khoản ' . mb_strtolower($catName) . ' giá rẻ', mb_strtolower($catName), SITENAME];
            $seoSlug  = !empty($activeCategory['seo_slug']) ? $activeCategory['seo_slug'] : ($activeCategory['slug'] ?? '');
            $canonical = Url::category($seoSlug) . ($page > 1 ? '?page=' . $page : '');
            $robots = ($q !== '' || !empty($_GET['sort'])) ? 'noindex,follow' : 'index,follow';
            $prevUrl = ($page > 1 && $q === '' && empty($_GET['sort'])) ? Url::category($seoSlug) . ($page > 2 ? '?page=' . ($page - 1) : '') : null;
            $nextUrl = ($page < $totalPages && $q === '' && empty($_GET['sort'])) ? Url::category($seoSlug) . '?page=' . ($page + 1) : null;
            Seo::set([
                'title'       => $seoTitle,
                'description' => $seoDesc,
                'keywords'    => $seoKey,
                'image'       => url('assets/images/gemini_share.webp'),
                'canonical'   => $canonical,
                'type'        => 'website',
                'robots'      => $robots,
                'prev'        => $prevUrl,
                'next'        => $nextUrl,
                'structured'  => $this->productItemListSchema($products, $canonical),
            ]);
        } else {
            $homeSeo = Seo::defaults($tab === 'products' ? 'products' : ($tab === 'blog' ? 'blog' : 'home'));
            $canonical = Url::home();
            $prevUrl = null;
            $nextUrl = null;

            if ($tab === 'products') {
                $canonical = Url::products() . ($page > 1 ? '?page=' . $page : '');
                if ($page > 1 && $q === '' && empty($_GET['sort'])) {
                    $prevUrl = Url::products() . ($page > 2 ? '?page=' . ($page - 1) : '');
                }
                if ($page < $totalPages && $q === '' && empty($_GET['sort'])) {
                    $nextUrl = Url::products() . '?page=' . ($page + 1);
                }
            } elseif ($tab === 'blog') {
                $canonical = Url::blogs() . ($blogPage > 1 ? '?page=' . $blogPage : '');
                if ($blogPage > 1) {
                    $prevUrl = Url::blogs() . ($blogPage > 2 ? '?page=' . ($blogPage - 1) : '');
                }
                if ($blogPage < $totalBlogPages) {
                    $nextUrl = Url::blogs() . '?page=' . ($blogPage + 1);
                }
            }

            $robots = ($q !== '' || !empty($_GET['sort'])) ? 'noindex,follow' : 'index,follow';
            Seo::set([
                'title'       => $homeSeo['title'] ?? 'Tài khoản AI Premium - Gemini Advanced, ChatGPT, Copilot',
                'description' => $homeSeo['description'] ?? 'Cung cấp tài khoản Gemini Advanced (Google One AI Premium), ChatGPT Plus, YouTube Premium, GitHub Copilot giá tốt nhất. Kích hoạt tự động, bảo hành 1 đổi 1 uy tín.',
                'keywords'    => $homeSeo['keywords'] ?? ['tài khoản gemini advanced', 'google gemini advanced', 'tài khoản chatgpt plus', 'youtube premium', 'github copilot', 'tài khoản ai', SITENAME],
                'image'       => url('assets/images/gemini_share.webp'),
                'canonical'   => $canonical,
                'type'        => 'website',
                'robots'      => $robots,
                'prev'        => $prevUrl,
                'next'        => $nextUrl,
                'structured'  => $tab === 'products' ? $this->productItemListSchema($products, $canonical) : null,
            ]);
        }
        $allProds = Product::getAll();
        
        $bestSellingProducts = $allProds;
        usort($bestSellingProducts, function($a, $b) {
            return $b['sold_count'] <=> $a['sold_count'];
        });
        $bestSellingProducts = array_slice($bestSellingProducts, 0, 8);

        $highestRatedProducts = $allProds;
        usort($highestRatedProducts, function($a, $b) {
            $ratingA = (float)($a['rating'] ?? 0);
            $ratingB = (float)($b['rating'] ?? 0);
            if ($ratingA == $ratingB) {
                return $b['sold_count'] <=> $a['sold_count'];
            }
            return $ratingB <=> $ratingA;
        });
        $highestRatedProducts = array_slice($highestRatedProducts, 0, 8);

        $newestProducts = $allProds;
        usort($newestProducts, function($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });
        $newestProducts = array_slice($newestProducts, 0, 8);

        $this->view('layout', [
            'view' => 'home',
            'products' => $products,
            'categories' => $categories,
            'blogs' => $blogs,
            'blogPage' => $blogPage,
            'totalBlogPages' => $totalBlogPages,
            'totalBlogs' => $totalBlogs,
            'recentOrders' => $recentOrders,
            'settings' => $settings,
            'tab' => $tab,
            'categorySlug' => $categorySlug,
            'searchQuery' => $q,
            'sort' => $sort,
            'page' => $page,
            'totalPages' => $totalPages,
            'systemStats' => $systemStats,
            'recentReviews' => $recentReviews,
            'bestSellingProducts' => $bestSellingProducts,
            'highestRatedProducts' => $highestRatedProducts,
            'newestProducts' => $newestProducts
        ]);
    }

    public function blogDetail() {
        $id = $_GET['id'] ?? null;
        $blog = $id ? Blog::getBySlugOrId($id) : null;

        if (!$blog) {
            // Smart Redirect instead of 404 (Item 5)
            if ($id) {
                $slug = Seo::slugify($id);
                $keywordPath = APP_ROOT . '/config/seo_keywords.json';
                if (file_exists($keywordPath)) {
                    $seoData = json_decode(file_get_contents($keywordPath), true);
                    $keywords = isset($seoData['keywords']) ? array_keys($seoData['keywords']) : [];
                    foreach ($keywords as $kw) {
                        if (strpos($slug, $kw) !== false) {
                            header('Location: ' . Url::search($kw), true, 302);
                            exit;
                        }
                    }
                }
            }
            header('Location: ' . Url::blogs(), true, 302);
            exit;
        }

        $allProducts = Product::getAll();
        shuffle($allProducts);
        $sidebarProducts = array_slice($allProducts, 0, 3);

        $excerptSource = trim((string) ($blog['description'] ?? '')) ?: (string) ($blog['content'] ?? '');
        $excerpt = Seo::truncate(strip_tags($excerptSource), 200);
        $seoTitle = !empty($blog['seo_title']) ? $blog['seo_title'] : ($blog['title'] ?? 'Bài viết');
        $seoDesc  = !empty($blog['seo_description']) ? $blog['seo_description'] : $excerpt;
        $seoKey   = !empty($blog['seo_keywords']) ? explode(',', $blog['seo_keywords']) : [$blog['title'] ?? '', SITENAME];
        Seo::set([
            'title'       => $seoTitle,
            'description' => $seoDesc,
            'keywords'    => $seoKey,
            'image'       => $blog['image'] ?? '',
            'canonical'   => Url::blog($blog),
            'type'        => 'article',
            'structured'  => [
                '@context'      => 'https://schema.org',
                '@type'         => 'BlogPosting',
                'headline'      => $blog['title'] ?? '',
                'image'         => $blog['image'] ?? '',
                'datePublished' => !empty($blog['created_at']) ? date('c', strtotime($blog['created_at'])) : null,
                'author'        => ['@type' => 'Organization', 'name' => SITENAME],
                'publisher'     => ['@type' => 'Organization', 'name' => SITENAME],
                'mainEntityOfPage' => Url::blog($blog),
                'description'   => $seoDesc,
            ],
        ]);

        $this->view('layout', [
            'view' => 'blog_detail',
            'blog' => $blog,
            'sidebarProducts' => $sidebarProducts,
            'settings' => $this->settings
        ]);
    }

    public function productDetail() {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$id) {
            header('Location: ' . url());
            exit;
        }

        $product = Product::getBySlugOrId($id);
        $settings = $this->settings;

        if (!$product) {
            $slug = Seo::slugify($id);
            // Split the slug into keywords
            $keywords = array_filter(explode('-', $slug), function($w) {
                return strlen($w) > 2;
            });

            $relatedProducts = [];
            $relatedBlogs = [];

            if (!empty($keywords)) {
                // Search related products
                $allProducts = Product::getAll();
                foreach ($allProducts as $p) {
                    $pSlug = Seo::slugify($p['title'] ?? '');
                    $pDescSlug = Seo::slugify($p['description'] ?? '');
                    $matches = 0;
                    foreach ($keywords as $kw) {
                        if (strpos($pSlug, $kw) !== false || strpos($pDescSlug, $kw) !== false) {
                            $matches++;
                        }
                    }
                    if ($matches > 0) {
                        $relatedProducts[] = [
                            'item' => $p,
                            'matches' => $matches
                        ];
                    }
                }
                usort($relatedProducts, function($a, $b) {
                    return $b['matches'] <=> $a['matches'];
                });
                $relatedProducts = array_map(function($x) { return $x['item']; }, $relatedProducts);
                // Limit to top 8 related products
                $relatedProducts = array_slice($relatedProducts, 0, 8);

                // Search related blogs
                $allBlogs = Blog::getAll();
                foreach ($allBlogs as $b) {
                    $bSlug = Seo::slugify($b['title'] ?? '');
                    $bDescSlug = Seo::slugify($b['description'] ?? '');
                    $matches = 0;
                    foreach ($keywords as $kw) {
                        if (strpos($bSlug, $kw) !== false || strpos($bDescSlug, $kw) !== false) {
                            $matches++;
                        }
                    }
                    if ($matches > 0) {
                        $relatedBlogs[] = [
                            'item' => $b,
                            'matches' => $matches
                        ];
                    }
                }
                usort($relatedBlogs, function($a, $b) {
                    return $b['matches'] <=> $a['matches'];
                });
                $relatedBlogs = array_map(function($x) { return $x['item']; }, $relatedBlogs);
                // Limit to top 6 related blogs
                $relatedBlogs = array_slice($relatedBlogs, 0, 6);
            }

            // Set SEO for this suggestions page
            Seo::set([
                'title'       => 'Không tìm thấy sản phẩm - ' . SITENAME,
                'description' => 'Sản phẩm bạn đang tìm kiếm không tồn tại hoặc đã tạm dừng bán. Vui lòng xem các sản phẩm liên quan bên dưới hoặc liên hệ admin đặt hàng.',
                'keywords'    => ['404', 'không tìm thấy', SITENAME],
                'canonical'   => url('san-pham/' . $id),
                'type'        => 'website',
            ]);

            $this->view('layout', [
                'view' => 'not-found-suggestions',
                'relatedProducts' => $relatedProducts,
                'relatedBlogs' => $relatedBlogs,
                'settings' => $settings
            ]);
            exit;
        }

        $excerpt = Seo::truncate(strip_tags($product['description'] ?? ($product['feature_text'] ?? '')), 200);
        $seoTitle = !empty($product['seo_title']) ? $product['seo_title'] : ($product['title'] ?? 'Sản phẩm');
        $seoDesc  = !empty($product['seo_description']) ? $product['seo_description'] : ($excerpt ?: 'Mua ' . ($product['title'] ?? 'sản phẩm') . ' chính chủ, bảo hành 1 đổi 1.');
        $seoKey   = !empty($product['seo_keywords']) ? explode(',', $product['seo_keywords']) : [$product['title'] ?? '', $product['category'] ?? '', SITENAME];
        Seo::set([
            'title'       => $seoTitle,
            'description' => $seoDesc,
            'image'       => image_url($product['image'] ?? ''),
            'canonical'   => Url::product($product),
            'type'        => 'product',
            'keywords'    => $seoKey,
            'structured'  => [
                '@context'    => 'https://schema.org',
                '@type'       => 'Product',
                'name'        => $product['title'] ?? '',
                'image'       => image_url($product['image'] ?? ''),
                'description' => $seoDesc,
                'sku'         => 'prod_' . ($product['id'] ?? ''),
                'mpn'         => 'mpn_' . ($product['id'] ?? ''),
                'brand'       => [
                    '@type' => 'Brand',
                    'name'  => SITENAME
                ],
                'offers'      => $this->productPriceData($product)['offer'],
                'aggregateRating' => [
                    '@type'       => 'AggregateRating',
                    'ratingValue' => (string) ((isset($product['rating']) && (float)$product['rating'] >= 1.0) ? $product['rating'] : 5.0),
                    'reviewCount' => (string) (max(1, (int) ($product['sold_count'] ?? 10))),
                    'bestRating'  => '5',
                    'worstRating' => '1',
                ],
                'review' => [
                    '@type' => 'Review',
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => '5',
                        'bestRating'  => '5',
                        'worstRating' => '1',
                    ],
                    'author' => [
                        '@type' => 'Person',
                        'name'  => 'Khách hàng đã xác thực',
                    ],
                    'reviewBody' => 'Tài khoản kích hoạt tự động nhanh, dùng ổn định và bảo hành uy tín.',
                ],
            ],
        ]);

        $this->view('layout', [
            'view' => 'product-detail',
            'product' => $product,
            'settings' => $settings
        ]);
    }

    /**
     * Render captcha image endpoint
     */
    public function captcha() {
        Captcha::render();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (Auth::check()) {
                header('Location: ' . Url::home());
                exit;
            }
            $this->view('layout', [
                'view' => 'auth/register',
                'settings' => $this->settings,
                'pageTitle' => 'Đăng ký tài khoản - ' . SITENAME,
            ]);
            return;
        }

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);

        // 1. CSRF Token Validation
        if (!Csrf::validate()) {
            $msg = 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn. Vui lòng thử lại.';
            if ($isAjax) {
                http_response_code(419);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $_SESSION['flash_error'] = $msg;
            header('Location: ' . Url::register());
            exit;
        }

        // 2. Honeypot check (Antispam trap)
        if (!empty($_POST['website_url_check'])) {
            if ($isAjax) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Phát hiện hành vi không hợp lệ.']);
                exit;
            }
            header('Location: ' . Url::register());
            exit;
        }

        // 3. Rate limiting check
        if (!Auth::checkRegisterRateLimit(5, 600)) {
            $msg = 'Bạn đã thử đăng ký quá nhiều lần. Vui lòng thử lại sau 10 phút.';
            if ($isAjax) {
                http_response_code(429);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $_SESSION['flash_error'] = $msg;
            $_SESSION['register_error'] = $msg;
            header('Location: ' . Url::register());
            exit;
        }

        Auth::recordRegisterAttempt();

        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirmation'] ?? '';
        $captcha = trim($_POST['captcha'] ?? '');

        // 4. Captcha verification
        if (!Captcha::verify($captcha)) {
            $msg = 'Mã bảo vệ (Captcha) không chính xác hoặc đã hết hạn. Vui lòng thử lại.';
            if ($isAjax) {
                http_response_code(422);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'field' => 'captcha', 'message' => $msg]);
                exit;
            }
            $_SESSION['flash_error'] = $msg;
            $_SESSION['register_error'] = $msg;
            $_SESSION['old_register_name'] = $name;
            $_SESSION['old_register_email'] = $email;
            header('Location: ' . Url::register());
            exit;
        }

        // 5. Basic input validation
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            $msg = 'Vui lòng nhập đúng họ tên, email hợp lệ và mật khẩu tối thiểu 6 ký tự.';
            if ($isAjax) {
                http_response_code(422);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $_SESSION['flash_error'] = $msg;
            $_SESSION['register_error'] = $msg;
            $_SESSION['old_register_name'] = $name;
            $_SESSION['old_register_email'] = $email;
            header('Location: ' . Url::register());
            exit;
        }

        // 6. Confirm password validation
        if ($password !== $passwordConfirm) {
            $msg = 'Mật khẩu xác nhận không khớp. Vui lòng kiểm tra lại.';
            if ($isAjax) {
                http_response_code(422);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'field' => 'password_confirmation', 'message' => $msg]);
                exit;
            }
            $_SESSION['flash_error'] = $msg;
            $_SESSION['register_error'] = $msg;
            $_SESSION['old_register_name'] = $name;
            $_SESSION['old_register_email'] = $email;
            header('Location: ' . Url::register());
            exit;
        }

        // 7. Block synthetic / pentest / temporary disposable email domains
        $suspiciousDomains = ['lab-synth.dev', 'synthetic-lab.invalid', 'tempmail', 'dispostable', 'mailinator', '.invalid'];
        foreach ($suspiciousDomains as $domain) {
            if (strpos($email, $domain) !== false || strpos($name, 'pentest') !== false || strpos($email, 'pentest') !== false) {
                $msg = 'Tên hoặc Email không được chấp nhận.';
                if ($isAjax) {
                    http_response_code(422);
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $msg]);
                    exit;
                }
                $_SESSION['flash_error'] = $msg;
                $_SESSION['register_error'] = $msg;
                $_SESSION['old_register_name'] = $name;
                $_SESSION['old_register_email'] = $email;
                header('Location: ' . Url::register());
                exit;
            }
        }

        if (User::findByEmail($email)) {
            $msg = 'Email này đã được đăng ký tài khoản.';
            if ($isAjax) {
                http_response_code(409);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $_SESSION['flash_error'] = $msg;
            $_SESSION['register_error'] = $msg;
            $_SESSION['old_register_name'] = $name;
            $_SESSION['old_register_email'] = $email;
            header('Location: ' . Url::register());
            exit;
        }

        User::create($name, $email, $password);
        $user = User::findByEmail($email);
        Auth::login($user);

        // Thông báo Telegram khi có khách hàng đăng ký mới
        if (!empty($user)) {
            TelegramService::notifyNewUser($user, 'Đăng ký tài khoản');
        }

        $_SESSION['flash_success'] = 'Đăng ký tài khoản thành công.';
        $redirect = Url::home();
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'redirect' => $redirect]);
            exit;
        }
        header('Location: ' . $redirect);
        exit;
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (Auth::check()) {
                header('Location: ' . Url::home());
                exit;
            }
            $this->view('layout', [
                'view' => 'auth/login',
                'settings' => $this->settings,
                'pageTitle' => 'Đăng nhập tài khoản - ' . SITENAME,
            ]);
            return;
        }

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);

        if (!Auth::checkLoginRateLimit()) {
            $msg = 'Bạn đã thử đăng nhập quá nhiều lần. Vui lòng đợi vài phút.';
            if ($isAjax) {
                http_response_code(429);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $_SESSION['flash_error'] = $msg;
            $_SESSION['login_error'] = $msg;
            header('Location: ' . Url::login());
            exit;
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            Auth::recordFailedLogin();
            $msg = 'Email hoặc mật khẩu không đúng.';
            if ($isAjax) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $_SESSION['flash_error'] = $msg;
            $_SESSION['login_error'] = $msg;
            $_SESSION['old_login_email'] = $email;
            header('Location: ' . Url::login());
            exit;
        }

        if (($user['status'] ?? '') !== 'active') {
            Auth::recordFailedLogin();
            $msg = 'Tài khoản đang bị khóa.';
            if ($isAjax) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $_SESSION['flash_error'] = $msg;
            $_SESSION['login_error'] = $msg;
            $_SESSION['old_login_email'] = $email;
            header('Location: ' . Url::login());
            exit;
        }

        Auth::login($user);
        $_SESSION['flash_success'] = 'Đăng nhập thành công.';

        $redirect = (($user['role'] ?? 'user') === 'admin') ? url('index.php?action=adminDashboard') : Url::home();

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'redirect' => $redirect]);
            exit;
        }

        header('Location: ' . $redirect);
        exit;
    }

    /**
     * Forgot Password - Step 1: Request reset link via Email
     */
    public function forgot_password() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (Auth::check()) {
                header('Location: ' . Url::home());
                exit;
            }
            $this->view('layout', [
                'view' => 'auth/forgot-password',
                'settings' => $this->settings,
                'pageTitle' => 'Quên mật khẩu - ' . SITENAME,
            ]);
            return;
        }

        if (!Csrf::validate()) {
            $_SESSION['forgot_error'] = 'Yêu cầu không hợp lệ hoặc phiên đã hết hạn. Vui lòng thử lại.';
            header('Location: ' . Url::forgotPassword());
            exit;
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $captcha = trim($_POST['captcha'] ?? '');

        if (!Captcha::verify($captcha)) {
            $_SESSION['forgot_error'] = 'Mã bảo vệ (Captcha) không chính xác hoặc đã hết hạn. Vui lòng nhập lại.';
            $_SESSION['old_forgot_email'] = $email;
            header('Location: ' . Url::forgotPassword());
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['forgot_error'] = 'Vui lòng nhập địa chỉ email hợp lệ.';
            $_SESSION['old_forgot_email'] = $email;
            header('Location: ' . Url::forgotPassword());
            exit;
        }

        $user = User::findByEmail($email);
        if ($user && ($user['status'] ?? 'active') === 'active') {
            $token = PasswordReset::createToken($email);
            $resetUrl = Url::resetPassword($token, $email);
            $this->sendPasswordResetEmail($user, $resetUrl);
        }

        // Generic friendly message to prevent email enumeration
        $_SESSION['forgot_success'] = 'Chúng tôi đã gửi hướng dẫn đặt lại mật khẩu đến ' . htmlspecialchars($email) . '. Vui lòng kiểm tra hộp thư đến.';
        header('Location: ' . Url::forgotPassword());
        exit;
    }

    /**
     * Reset Password - Step 2: Validate token and update new password
     */
    public function reset_password() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (Auth::check()) {
                header('Location: ' . Url::home());
                exit;
            }
            $token = trim($_GET['token'] ?? '');
            $email = strtolower(trim($_GET['email'] ?? ''));
            $isValidToken = false;

            if ($token !== '' && $email !== '') {
                $isValidToken = (PasswordReset::findValid($email, $token) !== null);
            }

            $this->view('layout', [
                'view' => 'auth/reset-password',
                'settings' => $this->settings,
                'token' => $token,
                'email' => $email,
                'isValidToken' => $isValidToken,
                'pageTitle' => 'Đặt lại mật khẩu - ' . SITENAME,
            ]);
            return;
        }

        if (!Csrf::validate()) {
            $_SESSION['flash_error'] = 'Phiên làm việc đã hết hạn. Vui lòng thử lại.';
            header('Location: ' . Url::forgotPassword());
            exit;
        }

        $token = trim($_POST['token'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirmation'] ?? '';

        $resetRecord = PasswordReset::findValid($email, $token);
        if (!$resetRecord) {
            $_SESSION['reset_error'] = 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn (chỉ có hiệu lực trong 30 phút).';
            header('Location: ' . Url::resetPassword($token, $email));
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['reset_error'] = 'Mật khẩu mới phải có tối thiểu 6 ký tự.';
            header('Location: ' . Url::resetPassword($token, $email));
            exit;
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['reset_error'] = 'Mật khẩu xác nhận không khớp. Vui lòng nhập lại.';
            header('Location: ' . Url::resetPassword($token, $email));
            exit;
        }

        $user = User::findByEmail($email);
        if (!$user) {
            $_SESSION['reset_error'] = 'Không tìm thấy tài khoản người dùng tương ứng.';
            header('Location: ' . Url::forgotPassword());
            exit;
        }

        User::updatePassword($user['id'], $password);
        PasswordReset::deleteByEmail($email);

        $_SESSION['flash_success'] = 'Mật khẩu đã được cập nhật thành công! Vui lòng đăng nhập với mật khẩu mới.';
        header('Location: ' . Url::login());
        exit;
    }

    /**
     * Send password reset email via SMTP
     */
    private function sendPasswordResetEmail(array $user, string $resetUrl): bool {
        $settings = $this->settings;
        $host = trim($settings['smtp_host'] ?? '');
        $port = (int)($settings['smtp_port'] ?? 587);
        $secure = trim($settings['smtp_secure'] ?? 'tls');
        $userSmtp = trim($settings['smtp_user'] ?? '');
        $pass = trim($settings['smtp_pass'] ?? '');
        $fromName = trim($settings['smtp_from_name'] ?? SITENAME);
        $fromEmail = trim($settings['smtp_from_email'] ?? $userSmtp);

        if (empty($host) || empty($userSmtp) || empty($pass)) {
            error_log('SMTP not fully configured for password reset email');
            return false;
        }

        $subject = '[' . SITENAME . '] Đặt lại mật khẩu tài khoản của bạn';
        $userName = htmlspecialchars($user['name'] ?? 'Quý khách');
        $siteName = htmlspecialchars(SITENAME);

        $bodyHtml = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
                .card { max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; }
                .card-header { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); padding: 30px 24px; text-align: center; color: #ffffff; }
                .card-header h1 { margin: 0; font-size: 22px; font-weight: 700; }
                .card-body { padding: 32px 28px; line-height: 1.6; }
                .greeting { font-size: 16px; font-weight: 600; margin-bottom: 16px; color: #0f172a; }
                .btn-reset { display: inline-block; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: #ffffff !important; padding: 14px 28px; text-decoration: none; border-radius: 10px; font-weight: bold; margin: 20px 0; font-size: 15px; }
                .link-box { background: #f1f5f9; padding: 12px; border-radius: 8px; word-break: break-all; font-size: 12px; color: #64748b; margin-top: 15px; }
                .notice { font-size: 13px; color: #64748b; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
                .card-footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; }
            </style>
        </head>
        <body>
            <div class='card'>
                <div class='card-header'>
                    <h1>{$siteName}</h1>
                </div>
                <div class='card-body'>
                    <div class='greeting'>Xin chào {$userName},</div>
                    <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản <strong>" . htmlspecialchars($user['email']) . "</strong> trên hệ thống {$siteName}.</p>
                    <p>Vui lòng bấm vào nút bên dưới để tiến hành tạo mật khẩu mới:</p>
                    <div style='text-align: center;'>
                        <a href='{$resetUrl}' class='btn-reset' target='_blank'>Đặt lại mật khẩu</a>
                    </div>
                    <p style='font-size: 13px; color: #64748b;'>Hoặc bạn có thể sao chép liên kết sau và dán vào thanh địa chỉ của trình duyệt:</p>
                    <div class='link-box'>{$resetUrl}</div>
                    <div class='notice'>
                        <p><strong>Lưu ý quan trọng:</strong></p>
                        <ul>
                            <li>Liên kết này chỉ có hiệu lực trong vòng <strong>30 phút</strong>.</li>
                            <li>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này. Mật khẩu hiện tại của bạn vẫn được giữ nguyên và bảo mật.</li>
                        </ul>
                    </div>
                </div>
                <div class='card-footer'>
                    &copy; " . date('Y') . " {$siteName}. Thư này được tạo tự động, vui lòng không phản hồi.
                </div>
            </div>
        </body>
        </html>";

        require_once APP_ROOT . '/app/Core/SmtpMailer.php';
        $mailer = new SmtpMailer($host, $port, $secure, $userSmtp, $pass);
        return $mailer->send($fromEmail, $fromName, $user['email'], $subject, $bodyHtml);
    }

    public function logout() {
        Auth::logout();
        $_SESSION['flash_success'] = 'Đã đăng xuất.';
        header('Location: ' . url());
        exit;
    }

    /** Bước 1: Redirect user đến Google để xác thực */
    public function googleLogin() {
        if (!GoogleAuth::isConfigured()) {
            $_SESSION['flash_error'] = 'Đăng nhập Google chưa được cấu hình.';
            header('Location: ' . url());
            exit;
        }
        header('Location: ' . GoogleAuth::getAuthUrl());
        exit;
    }

    /** Bước 2: Xử lý callback từ Google sau khi user đồng ý */
    public function googleCallback() {
        // Kiểm tra lỗi từ Google
        if (!empty($_GET['error'])) {
            $_SESSION['flash_error'] = 'Đăng nhập Google bị hủy hoặc có lỗi.';
            header('Location: ' . url());
            exit;
        }

        $code  = trim($_GET['code'] ?? '');
        $state = trim($_GET['state'] ?? '');

        // Xác minh CSRF state
        if ($code === '' || !GoogleAuth::verifyState($state)) {
            $_SESSION['flash_error'] = 'Yêu cầu không hợp lệ. Vui lòng thử lại.';
            header('Location: ' . url());
            exit;
        }

        // Đổi code lấy access_token
        $tokens = GoogleAuth::fetchTokens($code);
        if (!$tokens) {
            $_SESSION['flash_error'] = 'Không thể lấy token từ Google. Vui lòng thử lại.';
            header('Location: ' . url());
            exit;
        }

        // Lấy thông tin user từ Google
        $info = GoogleAuth::fetchUserInfo($tokens['access_token']);
        if (!$info || empty($info['email'])) {
            $_SESSION['flash_error'] = 'Không thể lấy thông tin tài khoản Google.';
            header('Location: ' . url());
            exit;
        }

        $googleId = $info['id'];
        $email    = $info['email'];
        $name     = $info['name'];
        $avatar   = $info['picture'] ?? '';

        // Case 1: Đã có tài khoản liên kết Google ID
        $user = User::findByGoogleId($googleId);

        if (!$user) {
            // Case 2: Tìm tài khoản qua email (email đã đăng ký trước đó)
            $existing = User::findByEmail($email);
            if ($existing) {
                // Tự động liên kết Google ID vào tài khoản email cũ
                User::linkGoogleId((int) $existing['id'], $googleId, $avatar);
                $user = User::findByEmail($email);
            } else {
                // Case 3: Tạo tài khoản mới từ Google
                $user = User::createFromGoogle($name, $email, $googleId, $avatar);
                if (!$user) {
                    $_SESSION['flash_error'] = 'Không thể tạo tài khoản. Vui lòng thử lại.';
                    header('Location: ' . url());
                    exit;
                }
                // Thông báo Telegram khi có khách hàng mới đăng ký qua Google
                TelegramService::notifyNewUser($user, 'Đăng nhập Google lần đầu');
            }
        }

        // Kiểm tra tài khoản có bị khoá không
        if (($user['status'] ?? '') !== 'active') {
            $_SESSION['flash_error'] = 'Tài khoản này đã bị khoá.';
            header('Location: ' . url());
            exit;
        }

        // Đăng nhập thành công
        Auth::login($user);
        $_SESSION['flash_success'] = 'Đăng nhập với Google thành công! Xin chào ' . htmlspecialchars($name) . '.';

        if (($user['role'] ?? 'user') === 'admin') {
            header('Location: ' . url('index.php?action=adminDashboard'));
        } else {
            header('Location: ' . url());
        }
        exit;
    }

    public function profile() {
        $this->requireLogin();

        $user = User::findById($_SESSION['user']['id']);
        if (!$user) {
            unset($_SESSION['user']);
            $_SESSION['flash_error'] = 'Tài khoản không tồn tại.';
            header('Location: ' . url());
            exit;
        }

        $this->view('layout', [
            'view' => 'profile',
            'settings' => $this->settings,
            'user' => $user
        ]);
    }

    public function updatePassword() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('index.php?action=profile'));
            exit;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $user = User::findWithPasswordById($_SESSION['user']['id']);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            $_SESSION['flash_error'] = 'Mật khẩu hiện tại không đúng.';
            header('Location: ' . url('index.php?action=profile'));
            exit;
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['flash_error'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
            header('Location: ' . url('index.php?action=profile'));
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['flash_error'] = 'Xác nhận mật khẩu mới không khớp.';
            header('Location: ' . url('index.php?action=profile'));
            exit;
        }

        User::updatePassword($user['id'], $newPassword);
        $_SESSION['flash_success'] = 'Đổi mật khẩu thành công.';
        header('Location: ' . url('index.php?action=profile'));
        exit;
    }

    public function orderHistory() {
        $this->requireLogin();

        $email = $_SESSION['user']['email'] ?? '';
        $orders = [];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $totalPages = 1;
        
        $totalAll = 0;
        $totalCompleted = 0;
        $totalPending = 0;
        $totalSpent = 0;

        if ($email !== '') {
            $db = Database::getInstance();

            // Count overall stats for user (all pages)
            $statsStmt = $db->prepare(
                "SELECT 
                    COUNT(*) as total_all,
                    SUM(CASE WHEN status IN ('completed', 'processing') THEN 1 ELSE 0 END) as total_completed,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as total_pending,
                    SUM(CASE WHEN status IN ('completed', 'processing') THEN amount ELSE 0 END) as total_spent
                 FROM orders WHERE customer_email = ?"
            );
            $statsStmt->execute([$email]);
            $stats = $statsStmt->fetch();
            
            $totalAll = (int) ($stats['total_all'] ?? 0);
            $totalCompleted = (int) ($stats['total_completed'] ?? 0);
            $totalPending = (int) ($stats['total_pending'] ?? 0);
            $totalSpent = (float) ($stats['total_spent'] ?? 0);
            
            $totalPages = max(1, ceil($totalAll / $limit));

            // Fetch paginated orders
            $stmt = $db->prepare("SELECT * FROM orders WHERE customer_email = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, $email, PDO::PARAM_STR);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->bindValue(3, $offset, PDO::PARAM_INT);
            $stmt->execute();
            $orders = $stmt->fetchAll();

            foreach ($orders as &$o) {
                $decoded = !empty($o['delivered_items']) ? json_decode($o['delivered_items'], true) : [];
                $o['delivered_items'] = is_array($decoded) ? $decoded : [];
            }
            unset($o);
        }

        $this->view('layout', [
            'view'           => 'order-history',
            'settings'       => $this->settings,
            'user'           => $_SESSION['user'],
            'orders'         => $orders,
            'page'           => $page,
            'totalPages'     => $totalPages,
            'totalAll'       => $totalAll,
            'totalCompleted' => $totalCompleted,
            'totalPending'   => $totalPending,
            'totalSpent'     => $totalSpent
        ]);
    }

    public function searchIndex() {
        header('Content-Type: application/json');
        header('Cache-Control: public, max-age=120');

        $items = [];
        try {
            foreach (Product::getAll() as $p) {
                if (($p['status'] ?? 'active') === 'hidden') continue;
                $items[] = [
                    'type'  => 'product',
                    'id'    => $p['id'] ?? '',
                    'title' => $p['title'] ?? '',
                    'cat'   => $p['category'] ?? ($p['category_slug'] ?? ''),
                    'desc'  => $p['feature_text'] ?? '',
                    'image' => $p['image'] ?? '',
                    'price' => (float) ($p['price'] ?? 0),
                    'url'   => Url::product($p),
                ];
            }
        } catch (Throwable $e) {}

        try {
            foreach (Category::getAll() as $c) {
                $items[] = [
                    'type'  => 'category',
                    'id'    => (int) ($c['id'] ?? 0),
                    'title' => $c['name'] ?? '',
                    'cat'   => 'Danh mục',
                    'slug'  => $c['slug'] ?? '',
                    'icon'  => $c['icon'] ?? '',
                    'url'   => url('?category=' . urlencode($c['slug'] ?? '')),
                ];
            }
        } catch (Throwable $e) {}

        try {
            foreach (Blog::getAll() as $b) {
                $items[] = [
                    'type'  => 'blog',
                    'id'    => (int) ($b['id'] ?? 0),
                    'title' => $b['title'] ?? '',
                    'cat'   => 'Tạp chí',
                    'desc'  => mb_substr(strip_tags($b['description'] ?? ''), 0, 120),
                    'image' => $b['image'] ?? '',
                    'url'   => Url::blog($b),
                ];
            }
        } catch (Throwable $e) {}

        echo json_encode(['items' => $items], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function about() {
        $aboutSeo = Seo::defaults('about');
        Seo::set([
            'title'       => $aboutSeo['title'] ?? 'Giới thiệu',
            'description' => $aboutSeo['description'] ?? (Seo::truncate(strip_tags($this->settings['about_desc'] ?? ''), 200) ?: 'Giới thiệu về ' . SITENAME),
            'keywords'    => $aboutSeo['keywords'] ?? [],
            'image'       => $this->settings['about_image'] ?? '',
            'canonical'   => Url::about(),
            'type'        => 'website',
        ]);
        $this->view('layout', [
            'view' => 'about',
            'settings' => $this->settings
        ]);
    }

    public function contact() {
        $contactSeo = Seo::defaults('contact');
        Seo::set([
            'title'       => $contactSeo['title'] ?? 'Liên hệ',
            'description' => $contactSeo['description'] ?? (Seo::truncate(strip_tags($this->settings['contact_desc'] ?? ''), 200) ?: 'Thông tin liên hệ ' . SITENAME),
            'keywords'    => $contactSeo['keywords'] ?? [],
            'canonical'   => Url::contact(),
            'type'        => 'website',
        ]);
        $this->view('layout', [
            'view' => 'contact',
            'settings' => $this->settings
        ]);
    }

    public function submitContact() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . Url::contact());
            exit;
        }

        // 1. Honeypot trap check for bots
        if (!empty($_POST['website_url_check'])) {
            $_SESSION['flash_success'] = 'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm.';
            header('Location: ' . Url::contact());
            exit;
        }

        // 2. Rate Limiting (max 3 contact submissions per 10 mins per IP)
        $ip = SecurityLogger::getClientIp();
        $rateKey = 'contact_submit_' . md5($ip);
        $attempts = $_SESSION[$rateKey] ?? ['count' => 0, 'time' => time()];
        if ((time() - $attempts['time']) > 600) {
            $attempts = ['count' => 0, 'time' => time()];
        }
        if ($attempts['count'] >= 3) {
            $_SESSION['flash_error'] = 'Bạn đã gửi liên hệ quá nhiều lần. Vui lòng thử lại sau 10 phút.';
            header('Location: ' . Url::contact());
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // 3. Filter disposable / spam bot domains
        $spamDomains = ['meewignite.info', 'synthetic-lab.invalid', 'lab-synth.dev', 'tempmail', 'dispostable', 'guerrillamail', '10minutemail', '.invalid'];
        foreach ($spamDomains as $domain) {
            if (str_contains($email, $domain)) {
                $_SESSION['flash_error'] = 'Địa chỉ email không hợp lệ.';
                header('Location: ' . Url::contact());
                exit;
            }
        }

        // 4. Detect gibberish bot submissions (single word without spaces > 12 random chars)
        $isGibberishName = strlen($name) > 6 && !str_contains($name, ' ') && preg_match('/^[b-df-hj-np-tv-z]{5,}/i', $name);
        $isGibberishMessage = strlen($message) > 12 && !str_contains($message, ' ');
        if ($isGibberishName || $isGibberishMessage) {
            $_SESSION['flash_error'] = 'Nội dung liên hệ không hợp lệ.';
            header('Location: ' . Url::contact());
            exit;
        }

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $subject === '' || $message === '') {
            $_SESSION['flash_error'] = 'Vui lòng nhập đầy đủ thông tin liên hệ hợp lệ.';
            header('Location: ' . Url::contact());
            exit;
        }

        $attempts['count']++;
        $_SESSION[$rateKey] = $attempts;

        ContactMessage::create([
            'name' => mb_substr($name, 0, 190),
            'email' => mb_substr($email, 0, 190),
            'subject' => mb_substr($subject, 0, 255),
            'message' => $message,
            'ip_address' => $ip,
            'user_agent' => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
        ]);

        $_SESSION['flash_success'] = 'Đã gửi yêu cầu hỗ trợ. Admin sẽ kiểm tra và phản hồi sớm.';
        header('Location: ' . Url::contact());
        exit;
    }

    public function productAction() {
        $productId = $_POST['product_id'] ?? '';
        $variantIdx = (int)($_POST['variant_idx'] ?? 0);
        $actionType = $_POST['action_type'] ?? 'buy';

        $product = Product::getById($productId);
        if (!$product) {
            $_SESSION['flash_error'] = 'Sản phẩm không tồn tại.';
            header('Location: ' . url());
            exit;
        }

        $existingQuantity = 0;
        foreach (($_SESSION['cart'] ?? []) as $existingItem) {
            if (($existingItem['id'] ?? '') === $productId
                && (int) ($existingItem['variant_idx'] ?? 0) === $variantIdx) {
                $existingQuantity = (int) ($existingItem['quantity'] ?? 0);
                break;
            }
        }
        $requestedQuantity = $actionType === 'cart' ? $existingQuantity + 1 : 1;
        if (!Product::isPurchasable($product, $variantIdx, $requestedQuantity)) {
            $_SESSION['flash_error'] = 'Gói dịch vụ này đã hết hàng hoặc không còn đủ số lượng.';
            header('Location: ' . (empty($_SERVER['HTTP_REFERER']) ? Url::product($product) : $_SERVER['HTTP_REFERER']));
            exit;
        }

        if ($actionType === 'buy') {
            header('Location: ' . url('index.php?action=checkoutPage&product_id=' . urlencode($productId) . '&variant_idx=' . urlencode($variantIdx)));
            exit;
        }

        // Otherwise 'cart' addition
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $cartItem = $this->buildCartItem($product, $variantIdx, 1);
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if (($item['id'] ?? '') == $productId && (int) ($item['variant_idx'] ?? 0) === $cartItem['variant_idx']) {
                $item['quantity']++;
                $item = array_merge($item, array_diff_key($cartItem, ['quantity' => true]));
                $found = true;
                break;
            }
        }
        unset($item);
        if (!$found) {
            $_SESSION['cart'][] = $cartItem;
        }
        $_SESSION['flash_success'] = "Đã thêm " . $product['title'] . " vào giỏ hàng.";
        header('Location: ' . url('index.php?action=cart'));
        exit;
    }

    public function addToCart() {
        $productId = $_GET['id'] ?? '';
        $variantIdx = (int) ($_GET['variant_idx'] ?? 0);
        if (!$productId) {
            $_SESSION['flash_error'] = 'Sản phẩm không hợp lệ.';
            header('Location: ' . (empty($_SERVER['HTTP_REFERER']) ? url() : $_SERVER['HTTP_REFERER']));
            exit;
        }

        $product = Product::getById($productId);
        if (!$product) {
            $_SESSION['flash_error'] = 'Sản phẩm không tồn tại.';
            header('Location: ' . (empty($_SERVER['HTTP_REFERER']) ? url() : $_SERVER['HTTP_REFERER']));
            exit;
        }

        $existingQuantity = 0;
        foreach (($_SESSION['cart'] ?? []) as $existingItem) {
            if (($existingItem['id'] ?? '') === $productId
                && (int) ($existingItem['variant_idx'] ?? 0) === $variantIdx) {
                $existingQuantity = (int) ($existingItem['quantity'] ?? 0);
                break;
            }
        }
        if (!Product::isPurchasable($product, $variantIdx, $existingQuantity + 1)) {
            $_SESSION['flash_error'] = 'Gói dịch vụ này đã hết hàng hoặc không còn đủ số lượng.';
            header('Location: ' . (empty($_SERVER['HTTP_REFERER']) ? Url::product($product) : $_SERVER['HTTP_REFERER']));
            exit;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $cartItem = $this->buildCartItem($product, $variantIdx, 1);
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if (($item['id'] ?? '') == $productId && (int) ($item['variant_idx'] ?? 0) === $cartItem['variant_idx']) {
                $item['quantity']++;
                $item = array_merge($item, array_diff_key($cartItem, ['quantity' => true]));
                $found = true;
                break;
            }
        }
        unset($item);

        if (!$found) {
            $_SESSION['cart'][] = $cartItem;
        }

        $_SESSION['flash_success'] = "Đã thêm " . $product['title'] . " vào giỏ hàng.";
        header('Location: ' . (empty($_SERVER['HTTP_REFERER']) ? url() : $_SERVER['HTTP_REFERER']));
        exit;
    }

    public function cart() {
        $cart = $this->refreshCartPrices($_SESSION['cart'] ?? []);
        $_SESSION['cart'] = $cart;
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        Seo::set([
            'title'       => 'Giỏ hàng',
            'description' => 'Giỏ hàng của bạn tại ' . SITENAME,
            'canonical'   => Url::cart(),
            'robots'      => 'noindex,follow',
        ]);

        $this->view('layout', [
            'view' => 'cart',
            'settings' => $this->settings,
            'cart' => $cart,
            'total' => $total
        ]);
    }

    public function removeFromCart() {
        $productId = $_GET['id'] ?? '';
        $variantIdx = isset($_GET['variant_idx']) ? (int) $_GET['variant_idx'] : null;
        if (isset($_SESSION['cart'])) {
            $found = false;
            foreach ($_SESSION['cart'] as $key => $item) {
                $sameVariant = $variantIdx === null || (int) ($item['variant_idx'] ?? 0) === $variantIdx;
                if (($item['id'] ?? '') == $productId && $sameVariant) {
                    $found = true;
                    $_SESSION['flash_success'] = 'Đã xóa "' . $item['title'] . '" khỏi giỏ hàng.';
                    unset($_SESSION['cart'][$key]);
                    break;
                }
            }
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            if (!$found) {
                $_SESSION['flash_error'] = 'Không tìm thấy sản phẩm trong giỏ hàng.';
            }
        } else {
            $_SESSION['flash_error'] = 'Giỏ hàng đang trống.';
        }
        header('Location: ' . url('index.php?action=cart'));
        exit;
    }

    public function updateCartQuantity() {
        $productId = $_GET['id'] ?? '';
        $variantIdx = isset($_GET['variant_idx']) ? (int) $_GET['variant_idx'] : null;
        $change = (int)($_GET['change'] ?? 0);
        
        if (isset($_SESSION['cart']) && $productId !== '') {
            $found = false;
            foreach ($_SESSION['cart'] as $key => &$item) {
                $sameVariant = $variantIdx === null || (int) ($item['variant_idx'] ?? 0) === $variantIdx;
                if (($item['id'] ?? '') == $productId && $sameVariant) {
                    $found = true;
                    $newQuantity = $item['quantity'] + $change;
                    $product = Product::getById($productId);
                    if ($newQuantity > 0 && (!$product || !Product::isPurchasable($product, (int) ($item['variant_idx'] ?? 0), $newQuantity))) {
                        $_SESSION['flash_error'] = 'Kho không còn đủ số lượng để cập nhật giỏ hàng.';
                        break;
                    }
                    $item['quantity'] = $newQuantity;
                    if ($item['quantity'] <= 0) {
                        $_SESSION['flash_success'] = 'Đã xóa "' . $item['title'] . '" khỏi giỏ hàng.';
                        unset($_SESSION['cart'][$key]);
                    } else {
                        $_SESSION['flash_success'] = 'Cập nhật số lượng "' . $item['title'] . '" thành ' . $item['quantity'] . '.';
                    }
                    break;
                }
            }
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            if (!$found) {
                $_SESSION['flash_error'] = 'Không tìm thấy sản phẩm cần cập nhật.';
            }
        } else {
            $_SESSION['flash_error'] = 'Không thể cập nhật giỏ hàng.';
        }

        header('Location: ' . url('index.php?action=cart'));
        exit;
    }

    private function buildCartItem(array $product, int $variantIdx = 0, int $quantity = 1): array {
        $options = is_array($product['options'] ?? null) ? $product['options'] : [];
        if (!isset($options[$variantIdx])) {
            $variantIdx = 0;
        }
        $variant = $options[$variantIdx] ?? [];
        $price = (float) ($variant['price'] ?? $product['price'] ?? 0);
        $variantName = trim((string) ($variant['name'] ?? ''));

        return [
            'id' => $product['id'],
            'variant_idx' => $variantIdx,
            'variant_name' => $variantName,
            'title' => $product['title'],
            'price' => $price,
            'image' => $product['image'],
            'quantity' => max(1, $quantity)
            ,'stock' => max(0, (int) ($variant['stock'] ?? 0))
            ,'available' => Product::isPurchasable($product, $variantIdx, max(1, $quantity))
        ];
    }

    private function refreshCartPrices(array $cart): array {
        $refreshed = [];
        foreach ($cart as $item) {
            $product = Product::getById($item['id'] ?? '');
            if (!$product) {
                continue;
            }
            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $refreshed[] = $this->buildCartItem($product, (int) ($item['variant_idx'] ?? 0), $quantity);
        }
        return $refreshed;
    }

    private function productPriceData(array $product): array {
        $options = is_array($product['options'] ?? null) ? $product['options'] : [];
        $prices = [];
        foreach ($options as $option) {
            $price = (float) ($option['price'] ?? 0);
            if ($price > 0) {
                $prices[] = $price;
            }
        }
        if (empty($prices)) {
            $prices[] = (float) ($product['price'] ?? 0);
        }

        $lowPrice = min($prices);
        $highPrice = max($prices);
        $availability = ($product['status'] ?? 'active') === 'active'
            ? 'https://schema.org/InStock'
            : 'https://schema.org/OutOfStock';
        $url = Url::product($product);

        $returnPolicy = [
            '@type' => 'MerchantReturnPolicy',
            'applicableCountry' => 'VN',
            'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
            'merchantReturnDays' => 30,
            'returnFees' => 'https://schema.org/FreeReturn',
            'returnMethod' => 'https://schema.org/ReturnByMail',
        ];

        $shippingDetails = [
            '@type' => 'OfferShippingDetails',
            'shippingRate' => [
                '@type' => 'MonetaryAmount',
                'value' => '0',
                'currency' => 'VND',
            ],
            'shippingDestination' => [
                '@type' => 'DefinedRegion',
                'addressCountry' => 'VN',
            ],
            'deliveryTime' => [
                '@type' => 'ShippingDeliveryTime',
                'handlingTime' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => 0,
                    'maxValue' => 1,
                    'unitCode' => 'DAY',
                ],
                'transitTime' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => 0,
                    'maxValue' => 1,
                    'unitCode' => 'DAY',
                ],
            ],
        ];

        $offer = count($prices) > 1 ? [
            '@type'         => 'AggregateOffer',
            'priceCurrency' => 'VND',
            'lowPrice'      => (string) $lowPrice,
            'highPrice'     => (string) $highPrice,
            'offerCount'    => (string) count($prices),
            'availability'  => $availability,
            'url'           => $url,
            'hasMerchantReturnPolicy' => $returnPolicy,
            'shippingDetails' => $shippingDetails,
        ] : [
            '@type'         => 'Offer',
            'priceCurrency' => 'VND',
            'price'         => (string) $lowPrice,
            'priceValidUntil'=> date('Y-12-31'),
            'valueAddedTaxIncluded' => 'true',
            'availability'  => $availability,
            'url'           => $url,
            'hasMerchantReturnPolicy' => $returnPolicy,
            'shippingDetails' => $shippingDetails,
        ];

        return [
            'price' => $lowPrice,
            'offer' => $offer,
        ];
    }

    private function productItemListSchema(array $products, string $url): array {
        $items = [];
        $position = 1;
        foreach ($products as $product) {
            if (($product['status'] ?? 'active') === 'hidden') {
                continue;
            }
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'url' => Url::product($product),
                'name' => $product['title'] ?? '',
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'url' => $url,
            'itemListElement' => $items,
        ];
    }

    // Search Helper Logic
    private static $ALIASES = [
        'gpt' => 'chatgpt', 'vgpt' => 'chatgpt', 'chat' => 'chatgpt', 'openai' => 'chatgpt',
        'git' => 'github', 'gh' => 'github', 'cop' => 'copilot', 'copilot' => 'githubcopilot',
        'yt' => 'youtube', 'ytb' => 'youtube', 'youtub' => 'youtube',
        'nf' => 'netflix', 'netf' => 'netflix',
        'ggdrive' => 'googledrive', 'gdrive' => 'googledrive',
        'spo' => 'spotify', 'spt' => 'spotify',
        'cs' => 'cursor', 'cur' => 'cursor',
        'fb' => 'facebook', 'tiktok' => 'tiktok', 'tik' => 'tiktok',
        'cl' => 'claude', 'claude' => 'claudeai',
        'gemi' => 'gemini', 'bard' => 'gemini',
        'mid' => 'midjourney', 'mj' => 'midjourney',
    ];

    private function normalizeString($s) {
        $s = mb_strtolower($s, 'UTF-8');
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        ];
        foreach ($unicode as $nonDiacritic => $diacriticPattern) {
            $s = preg_replace("/($diacriticPattern)/i", $nonDiacritic, $s);
        }
        return $s;
    }

    private function getTokens($s) {
        $normalized = $this->normalizeString($s);
        $parts = preg_split('/[^a-z0-9]+/', $normalized);
        return array_filter($parts);
    }

    private function expandQuery($q) {
        $norm = $this->normalizeString($q);
        $noSpace = preg_replace('/[^a-z0-9]/', '', $norm);
        $variants = [$norm, $noSpace];

        if (isset(self::$ALIASES[$noSpace])) {
            $variants[] = self::$ALIASES[$noSpace];
        }

        foreach ($this->getTokens($q) as $t) {
            if (isset(self::$ALIASES[$t])) {
                $variants[] = self::$ALIASES[$t];
            }
        }

        return array_values(array_filter(array_unique($variants)));
    }

    private function getBigrams($s) {
        $out = [];
        $str = ' ' . $s . ' ';
        $len = mb_strlen($str, 'UTF-8');
        for ($i = 0; $i < $len - 1; $i++) {
            $out[] = mb_substr($str, $i, 2, 'UTF-8');
        }
        return array_unique($out);
    }

    private function diceCoef($a, $b) {
        if (empty($a) || empty($b)) return 0;
        $inter = count(array_intersect($a, $b));
        return (2 * $inter) / (count($a) + count($b));
    }

    private function scoreProduct($product, $queries) {
        $title = $product['title'] ?? '';
        $cat = $product['category'] ?? ($product['category_slug'] ?? '');
        $desc = $product['feature_text'] ?? '';
        
        $haystack = $this->normalizeString($title . ' ' . $cat . ' ' . $desc);
        $haystackJoined = preg_replace('/\s+/', '', $haystack);
        $best = 0;

        foreach ($queries as $q) {
            if ($q === '') continue;
            
            $titleNorm = $this->normalizeString($title);
            if ($titleNorm === $q) {
                $best = max($best, 1.0);
                continue;
            }
            if (strpos($titleNorm, $q) === 0) {
                $best = max($best, 0.95);
                continue;
            }
            if (strpos($haystack, $q) !== false) {
                $best = max($best, 0.85);
                continue;
            }
            if (strpos($haystackJoined, $q) !== false) {
                $best = max($best, 0.78);
                continue;
            }
            $tokens = $this->getTokens($title);
            $tokenMatches = false;
            foreach ($tokens as $t) {
                if (strpos($t, $q) === 0 || strpos($q, $t) === 0) {
                    $tokenMatches = true;
                    break;
                }
            }
            if ($tokenMatches) {
                $best = max($best, 0.72);
                continue;
            }
            
            $score = $this->diceCoef($this->getBigrams($q), $this->getBigrams($titleNorm));
            if ($score > $best) {
                $best = $score;
            }
        }

        return $best;
    }


    private function loginUser($user) {
        Auth::login($user);
    }

    private function requireLogin() {
        Auth::requireLogin();
    }

    public function submitReview() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url());
            exit;
        }

        $orderId = trim($_POST['order_id'] ?? '');
        $productId = trim($_POST['product_id'] ?? '');
        $rating = (int) ($_POST['rating'] ?? 5);
        $content = trim($_POST['content'] ?? '');
        $userId = $_SESSION['user']['id'];

        $redirectTo = $_POST['redirect_to'] ?? '';
        $fallbackUrl = !empty($redirectTo) ? $redirectTo : ($_SERVER['HTTP_REFERER'] ?? url('index.php?action=orderHistory'));

        if (!$orderId || !$productId || $rating < 1 || $rating > 5) {
            $_SESSION['flash_error'] = 'Thông tin đánh giá không hợp lệ.';
            header("Location: $fallbackUrl");
            exit;
        }

        // Verify order belongs to user and is completed or processing
        $order = Order::getById($orderId);
        $userEmail = $_SESSION['user']['email'] ?? '';
        $isOwner = ($order && (
            (!empty($order['customer_email']) && strtolower($order['customer_email']) === strtolower($userEmail)) ||
            (!empty($order['user_id']) && (int)$order['user_id'] === (int)$userId) ||
            (!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin')
        ));

        if (!$order || !$isOwner || !in_array($order['status'] ?? '', ['completed', 'processing'], true)) {
            $_SESSION['flash_error'] = 'Bạn không thể đánh giá đơn hàng này.';
            header("Location: $fallbackUrl");
            exit;
        }

        // Check if already reviewed
        if (Review::hasReviewed($orderId, $productId)) {
            $_SESSION['flash_error'] = 'Bạn đã đánh giá sản phẩm này trong đơn hàng này rồi.';
            header("Location: $fallbackUrl");
            exit;
        }

        if (Review::create($orderId, $productId, $userId, $rating, $content)) {
            $_SESSION['flash_success'] = 'Cảm ơn bạn đã gửi đánh giá!';
        } else {
            $_SESSION['flash_error'] = 'Có lỗi xảy ra, vui lòng thử lại sau.';
        }

        // Redirect back to referring page (order history or payment success)
        header("Location: $fallbackUrl");
        exit;
    }

    public function submitReviewReply() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url());
            exit;
        }

        $reviewId = (int) ($_POST['review_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        $currentUser = $_SESSION['user'] ?? null;

        if (!$reviewId || $content === '') {
            $_SESSION['flash_error'] = 'Dữ liệu không hợp lệ.';
            $referer = $_SERVER['HTTP_REFERER'] ?? url();
            header("Location: $referer");
            exit;
        }

        // Check if user is allowed to reply
        require_once APP_ROOT . '/app/Models/Review.php';
        if (!Review::canReply($reviewId, $currentUser)) {
            $_SESSION['flash_error'] = 'Bạn không có quyền trả lời đánh giá này hoặc đang chờ admin phản hồi.';
            $referer = $_SERVER['HTTP_REFERER'] ?? url();
            header("Location: $referer");
            exit;
        }

        // Add the reply
        if (Review::createReply($reviewId, $currentUser['id'], $content)) {
            $_SESSION['flash_success'] = 'Gửi phản hồi thành công!';
        } else {
            $_SESSION['flash_error'] = 'Có lỗi xảy ra, vui lòng thử lại sau.';
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? url();
        header("Location: $referer");
        exit;
    }

    /**
     * Resolves search keyword aliases to unified slugs.
     * Item 4: Keyword Alias Mapping
     */
    public function resolveKeywordSlug(string $keyword): string {
        $keyword = strtolower(trim($keyword));
        $aliases = [];
        $keywordPath = APP_ROOT . '/config/seo_keywords.json';
        if (file_exists($keywordPath)) {
            $seoData = json_decode(file_get_contents($keywordPath), true);
            $aliases = $seoData['aliases'] ?? [];
        }
        
        $slug = Seo::slugify($keyword);
        if (isset($aliases[$slug])) {
            return $aliases[$slug];
        }
        return $slug;
    }

    /**
     * Retrieves static metadata configurations for SEO keywords.
     * Item 1: SEO Keyword Landing Pages
     */
    public function getKeywordSeoData(string $keyword): array {
        $data = [];
        $keywordPath = APP_ROOT . '/config/seo_keywords.json';
        if (file_exists($keywordPath)) {
            $seoData = json_decode(file_get_contents($keywordPath), true);
            $data = $seoData['keywords'] ?? [];
        }

        $cleanKeyword = str_replace('-', ' ', $keyword);
        return $data[$keyword] ?? [
            'title' => 'Tài khoản ' . ucwords($cleanKeyword) . ' giá rẻ - Mua bán ' . ucwords($cleanKeyword) . ' tự động',
            'description' => 'Cung cấp tài khoản ' . $cleanKeyword . ' giá rẻ, chính chủ, kích hoạt tự động 24/7. Bảo hành 1 đổi 1 uy tín chất lượng.',
            'keywords' => ['tài khoản ' . $cleanKeyword . ' giá rẻ', 'mua tài khoản ' . $cleanKeyword, $cleanKeyword, SITENAME]
        ];
    }

    /**
     * Action to serve search keyword SEO landing page.
     * Item 1: Biến các trang kết quả tìm kiếm động thành URL tĩnh
     */
    public function searchKeyword() {
        $keyword = $_GET['keyword'] ?? '';
        if ($keyword === '') {
            header('Location: ' . Url::products());
            exit;
        }

        $resolved = $this->resolveKeywordSlug($keyword);
        if ($resolved !== $keyword) {
            header('Location: ' . Url::search($resolved), true, 301);
            exit;
        }

        $_GET['q'] = str_replace('-', ' ', $resolved);
        $_GET['tab'] = 'products';

        $this->index();
    }

    private function getOrCreateChatSessionId(): string {
        if (!empty($_SESSION['chat_session_id'])) {
            return $_SESSION['chat_session_id'];
        }
        if (!empty($_COOKIE['chat_session_id'])) {
            $_SESSION['chat_session_id'] = $_COOKIE['chat_session_id'];
            return $_COOKIE['chat_session_id'];
        }
        $newId = 'chat_' . bin2hex(random_bytes(12));
        $_SESSION['chat_session_id'] = $newId;
        setcookie('chat_session_id', $newId, time() + 365 * 86400, '/');
        return $newId;
    }

    public function chatGetMessages(): void {
        header('Content-Type: application/json');
        $sessionId = $this->getOrCreateChatSessionId();
        $messages = ChatMessage::getMessagesBySession($sessionId);
        
        $unreadCount = 0;
        foreach ($messages as $m) {
            if ($m['sender_type'] === 'admin' && (int)$m['is_read'] === 0) {
                $unreadCount++;
            }
        }

        if (!empty($_GET['mark_read'])) {
            ChatMessage::markAsRead($sessionId, 'admin');
        }

        $isWaiting = ChatMessage::isWaitingForAdminReply($sessionId, 10);

        echo json_encode([
            'success' => true,
            'session_id' => $sessionId,
            'messages' => $messages,
            'unread_count' => $unreadCount,
            'is_waiting' => $isWaiting
        ]);
        exit;
    }

    public function chatSendMessage(): void {
        header('Content-Type: application/json');
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid method']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $messageText = trim($input['message'] ?? $_POST['message'] ?? '');

        if ($messageText === '') {
            echo json_encode(['success' => false, 'message' => 'Nội dung tin nhắn không được để trống']);
            exit;
        }

        $sessionId = $this->getOrCreateChatSessionId();

        // 5s rate limit check
        if (!empty($_SESSION['last_chat_send_time'])) {
            $elapsed = time() - (int)$_SESSION['last_chat_send_time'];
            if ($elapsed < 5) {
                $wait = 5 - $elapsed;
                echo json_encode([
                    'success' => false,
                    'message' => "Vui lòng chờ {$wait}s để gửi tin nhắn tiếp theo."
                ]);
                exit;
            }
        }

        // Anti-spam: check if already sent 10 consecutive messages waiting for admin reply
        if (ChatMessage::isWaitingForAdminReply($sessionId, 10)) {
            echo json_encode([
                'success' => false,
                'waiting_admin' => true,
                'message' => 'Bạn đã gửi 10 tin nhắn liên tiếp. Vui lòng chờ Admin phản hồi trước khi gửi tiếp nhé.'
            ]);
            exit;
        }

        $user = $_SESSION['user'] ?? null;
        $userId = $user['id'] ?? null;
        $senderName = $user['name'] ?? 'Khách hàng';

        $ok = ChatMessage::sendMessage($sessionId, 'user', $messageText, $userId, $senderName);
        if ($ok) {
            $_SESSION['last_chat_send_time'] = time();

            // Trigger Telegram Notification to Admin
            try {
                if (class_exists('TelegramService')) {
                    TelegramService::notifyNewChatMessage(
                        ['name' => $senderName, 'email' => ($user['email'] ?? "Session: {$sessionId}")],
                        $messageText
                    );
                }
            } catch (Throwable $ignored) {}

            $messages = ChatMessage::getMessagesBySession($sessionId);
            $isWaiting = ChatMessage::isWaitingForAdminReply($sessionId, 10);
            echo json_encode([
                'success' => true,
                'session_id' => $sessionId,
                'messages' => $messages,
                'is_waiting' => $isWaiting
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể gửi tin nhắn']);
        }
        exit;
    }

    public function chatUploadImage(): void {
        header('Content-Type: application/json');
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid method']);
            exit;
        }

        if (empty($_FILES['image'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng chọn hình ảnh']);
            exit;
        }

        $sessionId = $this->getOrCreateChatSessionId();

        // 5s rate limit check
        if (!empty($_SESSION['last_chat_send_time'])) {
            $elapsed = time() - (int)$_SESSION['last_chat_send_time'];
            if ($elapsed < 5) {
                $wait = 5 - $elapsed;
                echo json_encode([
                    'success' => false,
                    'message' => "Vui lòng chờ {$wait}s để gửi hình ảnh tiếp theo."
                ]);
                exit;
            }
        }

        // Anti-spam: check if already sent 10 consecutive messages waiting for admin reply
        if (ChatMessage::isWaitingForAdminReply($sessionId, 10)) {
            echo json_encode([
                'success' => false,
                'waiting_admin' => true,
                'message' => 'Bạn đã gửi 10 tin nhắn liên tiếp. Vui lòng chờ Admin phản hồi trước khi gửi tiếp nhé.'
            ]);
            exit;
        }

        try {
            $uploaded = Upload::store($_FILES['image'], 'chat', Upload::IMAGE_MIMES);
            $imgUrl = $uploaded['url'];
            
            $user = $_SESSION['user'] ?? null;
            $userId = $user['id'] ?? null;
            $senderName = $user['name'] ?? 'Khách hàng';

            $imgMsg = '[img]' . $imgUrl . '[/img]';
            $ok = ChatMessage::sendMessage($sessionId, 'user', $imgMsg, $userId, $senderName);

            if ($ok) {
                $_SESSION['last_chat_send_time'] = time();

                // Trigger Telegram Notification to Admin (tin nhắn văn bản nhẹ, không gửi payload hình ảnh)
                try {
                    if (class_exists('TelegramService')) {
                        TelegramService::notifyNewChatMessage(
                            ['name' => $senderName, 'email' => ($user['email'] ?? "Session: {$sessionId}")],
                            '📷 [Khách vừa gửi 1 hình ảnh]'
                        );
                    }
                } catch (Throwable $ignored) {}

                $messages = ChatMessage::getMessagesBySession($sessionId);
                $isWaiting = ChatMessage::isWaitingForAdminReply($sessionId, 10);
                echo json_encode([
                    'success' => true,
                    'session_id' => $sessionId,
                    'messages' => $messages,
                    'url' => $imgUrl,
                    'is_waiting' => $isWaiting
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể lưu hình ảnh']);
            }
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

?>
