<?php
$popupOrders = $recentOrders ?? RecentOrder::getAll();
?>
<?php if (!empty($popupOrders)): ?>
<div id="recent-purchase-popup" class="recent-purchase-popup" aria-live="polite" role="status">
    <div class="recent-purchase-avatar" id="popup-avatar-text">L</div>
    <div class="recent-purchase-content">
        <div class="recent-purchase-header">
            <span class="recent-purchase-name" id="popup-customer-name">L*</span>
            <button type="button" class="recent-purchase-close" onclick="closePurchasePopup(event)" aria-label="Đóng thông báo">&times;</button>
        </div>
        <div class="recent-purchase-meta">
            vừa mua thành công &bull; <span id="popup-time">19 phút trước</span>
        </div>
        <a href="#" class="recent-purchase-product" id="popup-product-name" target="_blank" rel="noopener">ChatGPT Plus 1 Tháng Dùng Riêng</a>
    </div>
</div>
<script>
    window.recentOrdersData = <?php echo json_encode($popupOrders, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?>;
</script>
<?php endif; ?>
