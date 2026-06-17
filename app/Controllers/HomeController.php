<?php

class HomeController extends Controller {
    private $settings;

    public function __construct() {
        $this->settings = Setting::getAll();
    }

    public function index() {
        $products = Product::getAll();
        $categories = Category::getAll();
        $blogs = Blog::getAll();
        $recentOrders = RecentOrder::getAll();
        $settings = $this->settings;

        // Query system statistics dynamically
        $db = Database::getInstance();
        $completedCount = (int)$db->query("SELECT COUNT(*) FROM orders WHERE status IN ('completed', 'processing')")->fetchColumn();
        $userCount = (int)$db->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
        
        // Count rating distribution
        $ratingsCount = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        try {
            $distStmt = $db->query("SELECT rating, COUNT(*) as cnt FROM reviews GROUP BY rating");
            while ($row = $distStmt->fetch()) {
                $r = (int)$row['rating'];
                $ratingsCount[$r] = (int)$row['cnt'];
            }
        } catch (Throwable $ignored) {}
        
        $totalReviews = array_sum($ratingsCount);
        $pct5 = $totalReviews > 0 ? round(($ratingsCount[5] / $totalReviews) * 100) : 100;
        $pct4 = $totalReviews > 0 ? round(($ratingsCount[4] / $totalReviews) * 100) : 0;
        $pct1_3 = $totalReviews > 0 ? round((($ratingsCount[3] + $ratingsCount[2] + $ratingsCount[1]) / $totalReviews) * 100) : 0;

        $avgRating = $db->query("SELECT AVG(rating) FROM reviews")->fetchColumn();
        $avgRating = $avgRating ? round((float)$avgRating, 1) : 5.0;

        $systemStats = [
            'completed_orders' => $completedCount,
            'total_users'      => $userCount,
            'average_rating'   => $avgRating,
            'pct_5'            => $pct5,
            'pct_4'            => $pct4,
            'pct_1_3'          => $pct1_3
        ];

        // Fetch latest reviews
        $recentReviews = Review::getRecentReviews(6);

        // Detect if mobile browser
        $isMobile = false;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent)) {
            $isMobile = true;
        }

        // Current active tab: default is home on desktop, products on mobile
        $tab = $_GET['tab'] ?? ($isMobile ? 'products' : 'home');
        if (!in_array($tab, ['home', 'products', 'blog'])) {
            $tab = $isMobile ? 'products' : 'home';
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

        if ($tab === 'home') {
            // No pagination slicing for home tab (uses JS "Xem thêm")
            $page = 1;
            $totalPages = 1;
        } else {
            // Standard pagination for products tab: 3 rows of 4 products = 12
            $page = max(1, (int) ($_GET['page'] ?? 1));
            $limit = 12;
            $offset = ($page - 1) * $limit;

            $totalFilteredProducts = count($products);
            $totalPages = max(1, ceil($totalFilteredProducts / $limit));
            $products = array_slice($products, $offset, $limit);
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
                'image'       => url('assets/images/gemini_share.png'),
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
                'image'       => url('assets/images/gemini_share.png'),
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
            if ($tab === 'products') {
                $canonical = Url::products() . ($page > 1 ? '?page=' . $page : '');
            } elseif ($tab === 'blog') {
                $canonical = Url::blogs();
            }
            $robots = ($q !== '' || !empty($_GET['sort'])) ? 'noindex,follow' : 'index,follow';
            $prevUrl = ($tab === 'products' && $page > 1 && $q === '' && empty($_GET['sort']))
                ? Url::products() . ($page > 2 ? '?page=' . ($page - 1) : '')
                : null;
            $nextUrl = ($tab === 'products' && $page < $totalPages && $q === '' && empty($_GET['sort']))
                ? Url::products() . '?page=' . ($page + 1)
                : null;
            Seo::set([
                'title'       => $homeSeo['title'] ?? 'Tài khoản AI Premium - Gemini Advanced, ChatGPT, Copilot',
                'description' => $homeSeo['description'] ?? 'Cung cấp tài khoản Gemini Advanced (Google One AI Premium), ChatGPT Plus, YouTube Premium, GitHub Copilot giá tốt nhất. Kích hoạt tự động, bảo hành 1 đổi 1 uy tín.',
                'keywords'    => $homeSeo['keywords'] ?? ['tài khoản gemini advanced', 'google gemini advanced', 'tài khoản chatgpt plus', 'youtube premium', 'github copilot', 'tài khoản ai', SITENAME],
                'image'       => url('assets/images/gemini_share.png'),
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
            // Smart Redirect instead of 404 (Item 5)
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

            $categories = Category::getAll();
            foreach ($categories as $cat) {
                $catSlug = $cat['seo_slug'] ?: $cat['slug'];
                if (strpos($slug, $catSlug) !== false) {
                    header('Location: ' . Url::category($catSlug), true, 302);
                    exit;
                }
            }

            header('Location: ' . Url::products(), true, 302);
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
                    'ratingValue' => (string) ($product['rating'] ?? 5),
                    'reviewCount' => (string) (max(1, (int) ($product['sold_count'] ?? 10))),
                    'bestRating'  => '5',
                    'worstRating' => '1',
                ]
            ],
        ]);

        $this->view('layout', [
            'view' => 'product-detail',
            'product' => $product,
            'settings' => $settings
        ]);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url());
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            $_SESSION['flash_error'] = 'Vui lòng nhập đúng họ tên, email và mật khẩu tối thiểu 6 ký tự.';
            header('Location: ' . url());
            exit;
        }

        if (User::findByEmail($email)) {
            $_SESSION['flash_error'] = 'Email này đã được đăng ký.';
            header('Location: ' . url());
            exit;
        }

        User::create($name, $email, $password);
        $user = User::findByEmail($email);
        Auth::login($user);

        $_SESSION['flash_success'] = 'Đăng ký thành công.';
        header('Location: ' . url());
        exit;
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url());
            exit;
        }

        if (!Auth::checkLoginRateLimit()) {
            $_SESSION['flash_error'] = 'Bạn đã thử đăng nhập quá nhiều lần. Vui lòng đợi vài phút.';
            header('Location: ' . url());
            exit;
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            Auth::recordFailedLogin();
            $_SESSION['flash_error'] = 'Email hoặc mật khẩu không đúng.';
            header('Location: ' . url());
            exit;
        }

        if (($user['status'] ?? '') !== 'active') {
            Auth::recordFailedLogin();
            $_SESSION['flash_error'] = 'Tài khoản đang bị khóa.';
            header('Location: ' . url());
            exit;
        }

        Auth::login($user);
        $_SESSION['flash_success'] = 'Đăng nhập thành công.';

        if (($user['role'] ?? 'user') === 'admin') {
            header('Location: ' . url('index.php?action=adminDashboard'));
        } else {
            header('Location: ' . url());
        }
        exit;
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

        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $subject === '' || $message === '') {
            $_SESSION['flash_error'] = 'Vui lòng nhập đầy đủ thông tin liên hệ hợp lệ.';
            header('Location: ' . Url::contact());
            exit;
        }

        ContactMessage::create([
            'name' => mb_substr($name, 0, 190),
            'email' => mb_substr($email, 0, 190),
            'subject' => mb_substr($subject, 0, 255),
            'message' => $message,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
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
                    $item['quantity'] += $change;
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

        $offer = count($prices) > 1 ? [
            '@type'         => 'AggregateOffer',
            'priceCurrency' => 'VND',
            'lowPrice'      => (string) $lowPrice,
            'highPrice'     => (string) $highPrice,
            'offerCount'    => (string) count($prices),
            'availability'  => $availability,
            'url'           => $url,
        ] : [
            '@type'         => 'Offer',
            'priceCurrency' => 'VND',
            'price'         => (string) $lowPrice,
            'priceValidUntil'=> date('Y-12-31'),
            'valueAddedTaxIncluded' => 'true',
            'availability'  => $availability,
            'url'           => $url,
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

        $orderId = $_POST['order_id'] ?? '';
        $productId = $_POST['product_id'] ?? '';
        $rating = (int) ($_POST['rating'] ?? 5);
        $content = trim($_POST['content'] ?? '');
        $userId = $_SESSION['user']['id'];

        if (!$orderId || !$productId || $rating < 1 || $rating > 5) {
            $_SESSION['flash_error'] = 'Thông tin đánh giá không hợp lệ.';
            header('Location: ' . url('index.php?action=orderHistory'));
            exit;
        }

        // Verify order belongs to user and is completed
        $order = Order::getById($orderId);
        if (!$order || $order['customer_email'] !== $_SESSION['user']['email'] || $order['status'] !== 'completed') {
            $_SESSION['flash_error'] = 'Bạn không thể đánh giá đơn hàng này.';
            header('Location: ' . url('index.php?action=orderHistory'));
            exit;
        }

        // Check if already reviewed
        if (Review::hasReviewed($orderId, $productId)) {
            $_SESSION['flash_error'] = 'Bạn đã đánh giá sản phẩm này trong đơn hàng này rồi.';
            header('Location: ' . url('index.php?action=orderHistory'));
            exit;
        }

        if (Review::create($orderId, $productId, $userId, $rating, $content)) {
            $_SESSION['flash_success'] = 'Cảm ơn bạn đã gửi đánh giá!';
        } else {
            $_SESSION['flash_error'] = 'Có lỗi xảy ra, vui lòng thử lại sau.';
        }

        // Redirect back to referring page (order history or payment success)
        $referer = $_SERVER['HTTP_REFERER'] ?? url('index.php?action=orderHistory');
        header("Location: $referer");
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
}
?>
