<?php
$features = [
    [
        'icon' => 'fa-solid fa-wallet',
        'title' => 'Great Value',
        'desc' => 'Most <strong>popular brands</strong> with widest range of selection <strong>at best prices</strong>.'
    ],
    [
        'icon' => 'fa-solid fa-truck-fast',
        'title' => 'Nationwide Delivery',
        'desc' => 'Over 20,000 pincodes <strong>serviceable across India</strong>.'
    ],
    [
        'icon' => 'fa-solid fa-credit-card',
        'title' => 'Secure Payment',
        'desc' => 'Partnered with India\'s <strong>most popular and secure</strong> payment solutions.'
    ],
    [
        'icon' => 'fa-solid fa-shield-halved',
        'title' => 'Buyer Protection',
        'desc' => 'Committed to buyer interests to provide a smooth shopping experience.'
    ],
    [
        'icon' => 'fa-solid fa-headset',
        'title' => '365 Days Help Desk',
        'desc' => '<div class="feature-whatsapp"><i class="fa-brands fa-whatsapp"></i> +91 9999049135</div>'
    ]
];
?>

<div class="features-section">
    <div class="features-container">
        <?php foreach ($features as $feature): ?>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="<?php echo $feature['icon']; ?>"></i>
                </div>
                <div class="feature-content">
                    <div class="feature-title"><?php echo $feature['title']; ?></div>
                    <div class="feature-desc"><?php echo $feature['desc']; ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
