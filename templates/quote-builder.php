<?php
/**
 * Service Quote Builder - Frontend Template
 */
if (!defined('ABSPATH')) {
    exit;
}

$currency_symbol = get_option('sqb_currency_symbol', '$');
$currency_position = get_option('sqb_currency_position', 'before');
$vat_rate = get_option('sqb_vat_rate', 0);
?>

<div class="sqb-container" id="sqb-quote-builder">
    <div class="sqb-header">
        <h1><?php echo esc_html__('Service Quote Builder', 'service-quote-builder'); ?></h1>
        <p class="sqb-subtitle"><?php echo esc_html__('Build your custom automotive protection package', 'service-quote-builder'); ?></p>
    </div>

    <!-- Progress Steps -->
    <div class="sqb-progress-bar">
        <div class="sqb-progress-step active" data-step="1">
            <span class="sqb-step-number">1</span>
            <span class="sqb-step-label"><?php echo esc_html__('Vehicle', 'service-quote-builder'); ?></span>
        </div>
        <div class="sqb-progress-step" data-step="2">
            <span class="sqb-step-number">2</span>
            <span class="sqb-step-label"><?php echo esc_html__('Condition', 'service-quote-builder'); ?></span>
        </div>
        <div class="sqb-progress-step" data-step="3">
            <span class="sqb-step-number">3</span>
            <span class="sqb-step-label"><?php echo esc_html__('Polishing', 'service-quote-builder'); ?></span>
        </div>
        <div class="sqb-progress-step" data-step="4">
            <span class="sqb-step-number">4</span>
            <span class="sqb-step-label"><?php echo esc_html__('Protection', 'service-quote-builder'); ?></span>
        </div>
        <div class="sqb-progress-step" data-step="5">
            <span class="sqb-step-number">5</span>
            <span class="sqb-step-label"><?php echo esc_html__('Extras', 'service-quote-builder'); ?></span>
        </div>
        <div class="sqb-progress-step" data-step="6">
            <span class="sqb-step-number">6</span>
            <span class="sqb-step-label"><?php echo esc_html__('Details', 'service-quote-builder'); ?></span>
        </div>
    </div>

    <form id="sqb-form" class="sqb-form">
        <!-- Step 1: Vehicle Type Selection -->
        <div class="sqb-step active" data-step="1">
            <h2 class="sqb-step-title"><?php echo esc_html__('Select Your Vehicle Type', 'service-quote-builder'); ?></h2>
            <div class="sqb-grid sqb-vehicle-grid">
                <div class="sqb-card" data-value="small" data-price="0">
                    <div class="sqb-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 17h14M7 17l-2-6h14l-2 6M5 17a2 2 0 1 0 4 0 2 2 0 1 0-4 0M15 17a2 2 0 1 0 4 0 2 2 0 1 0-4 0M8 11h8M9 8h6"/>
                        </svg>
                    </div>
                    <h3><?php echo esc_html__('Small/Compact', 'service-quote-builder'); ?></h3>
                    <p class="sqb-card-examples"><?php echo esc_html__('Audi A3, BMW 2 Series, VW Golf GTI, Mini Cooper', 'service-quote-builder'); ?></p>
                </div>
                <div class="sqb-card" data-value="mid" data-price="50">
                    <div class="sqb-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 17h14M7 17l-2-6h14l-2 6M5 17a2 2 0 1 0 4 0 2 2 0 1 0-4 0M15 17a2 2 0 1 0 4 0 2 2 0 1 0-4 0M6 10h12M7 7h10"/>
                        </svg>
                    </div>
                    <h3><?php echo esc_html__('Mid Size', 'service-quote-builder'); ?></h3>
                    <p class="sqb-card-examples"><?php echo esc_html__('Lexus ES, Ford Fusion, Tesla Model 3, Chevy Malibu, Audi A5', 'service-quote-builder'); ?></p>
                </div>
                <div class="sqb-card" data-value="full" data-price="100">
                    <div class="sqb-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 17h14M7 17l-2-6h14l-2 6M5 17a2 2 0 1 0 4 0 2 2 0 1 0-4 0M15 17a2 2 0 1 0 4 0 2 2 0 1 0-4 0M6 9h12M7 6h10M9 4h6"/>
                        </svg>
                    </div>
                    <h3><?php echo esc_html__('Full Size', 'service-quote-builder'); ?></h3>
                    <p class="sqb-card-examples"><?php echo esc_html__('Large Sedan, Dodge Charger, BMW 5 Series, Tesla Model S', 'service-quote-builder'); ?></p>
                </div>
                <div class="sqb-card" data-value="large" data-price="150">
                    <div class="sqb-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 17h14M7 17l-2-6h14l-2 6M5 17a2 2 0 1 0 4 0 2 2 0 1 0-4 0M15 17a2 2 0 1 0 4 0 2 2 0 1 0-4 0M4 11h16M5 8h14M7 5h10M8 3h8"/>
                        </svg>
                    </div>
                    <h3><?php echo esc_html__('Large/X-Large', 'service-quote-builder'); ?></h3>
                    <p class="sqb-card-examples"><?php echo esc_html__('Large/XL SUV, Extended Cab Pickup, Range Rover, BMW 7 Series', 'service-quote-builder'); ?></p>
                </div>
            </div>
            <input type="hidden" name="vehicle_type" id="vehicle_type" value="">
            <input type="hidden" name="vehicle_price" id="vehicle_price" value="0">
        </div>

        <!-- Step 2: Paint Condition -->
        <div class="sqb-step" data-step="2">
            <h2 class="sqb-step-title"><?php echo esc_html__('Assess Your Paint Condition', 'service-quote-builder'); ?></h2>
            <div class="sqb-grid sqb-condition-grid">
                <div class="sqb-card sqb-card-cond" data-value="new" data-hours="0">
                    <div class="sqb-condition-visual sqb-condition-new">
                        <svg viewBox="0 0 100 60" fill="none">
                            <rect x="5" y="5" width="90" height="50" rx="3" fill="#e8f5e9" stroke="#4caf50" stroke-width="2"/>
                            <circle cx="30" cy="30" r="8" fill="#4caf50" opacity="0.3"/>
                            <circle cx="70" cy="25" r="5" fill="#4caf50" opacity="0.2"/>
                        </svg>
                    </div>
                    <h3><?php echo esc_html__('New Car / Near Perfect', 'service-quote-builder'); ?></h3>
                    <p class="sqb-we-say"><?php echo esc_html__('Perfect for brand new vehicles or those in excellent condition with minimal defects.', 'service-quote-builder'); ?></p>
                </div>
                <div class="sqb-card sqb-card-cond" data-value="light" data-hours="1">
                    <div class="sqb-condition-visual sqb-condition-light">
                        <svg viewBox="0 0 100 60" fill="none">
                            <rect x="5" y="5" width="90" height="50" rx="3" fill="#fff8e1" stroke="#ffc107" stroke-width="2"/>
                            <line x1="20" y1="15" x2="35" y2="30" stroke="#ffc107" stroke-width="1.5" opacity="0.5"/>
                            <line x1="60" y1="20" x2="75" y2="35" stroke="#ffc107" stroke-width="1.5" opacity="0.4"/>
                            <line x1="45" y1="35" x2="55" y2="45" stroke="#ffc107" stroke-width="1" opacity="0.3"/>
                        </svg>
                    </div>
                    <h3><?php echo esc_html__('Light Swirls', 'service-quote-builder'); ?></h3>
                    <p class="sqb-we-say"><?php echo esc_html__('Shows minor swirl marks and light scratches from washing. Ideal for enhancement polish.', 'service-quote-builder'); ?></p>
                </div>
                <div class="sqb-card sqb-card-cond" data-value="medium" data-hours="2">
                    <div class="sqb-condition-visual sqb-condition-medium">
                        <svg viewBox="0 0 100 60" fill="none">
                            <rect x="5" y="5" width="90" height="50" rx="3" fill="#fff3e0" stroke="#ff9800" stroke-width="2"/>
                            <line x1="15" y1="10" x2="40" y2="35" stroke="#ff9800" stroke-width="2" opacity="0.6"/>
                            <line x1="55" y1="15" x2="85" y2="45" stroke="#ff9800" stroke-width="2" opacity="0.7"/>
                            <line x1="25" y1="40" x2="50" y2="50" stroke="#ff9800" stroke-width="1.5" opacity="0.5"/>
                        </svg>
                    </div>
                    <h3><?php echo esc_html__('Large Swirls & Some Deep Scratches', 'service-quote-builder'); ?></h3>
                    <p class="sqb-we-say"><?php echo esc_html__('Visible scratches and swirl marks require more intensive polishing work.', 'service-quote-builder'); ?></p>
                </div>
                <div class="sqb-card sqb-card-cond" data-value="deep" data-hours="3">
                    <div class="sqb-condition-visual sqb-condition-deep">
                        <svg viewBox="0 0 100 60" fill="none">
                            <rect x="5" y="5" width="90" height="50" rx="3" fill="#ffebee" stroke="#f44336" stroke-width="2"/>
                            <line x1="10" y1="8" x2="45" y2="43" stroke="#f44336" stroke-width="3" opacity="0.8"/>
                            <line x1="50" y1="10" x2="90" y2="50" stroke="#f44336" stroke-width="3" opacity="0.9"/>
                            <line x1="20" y1="45" x2="60" y2="55" stroke="#f44336" stroke-width="2" opacity="0.7"/>
                            <line x1="70" y1="20" x2="88" y2="38" stroke="#f44336" stroke-width="2" opacity="0.6"/>
                        </svg>
                    </div>
                    <h3><?php echo esc_html__('Deep Scratches On All Panels', 'service-quote-builder'); ?></h3>
                    <p class="sqb-we-say"><?php echo esc_html__('Significant defects on all paint surfaces requiring extensive correction work.', 'service-quote-builder'); ?></p>
                </div>
            </div>
            <input type="hidden" name="paint_condition" id="paint_condition" value="">
        </div>

        <!-- Step 3: Polishing Selection -->
        <div class="sqb-step" data-step="3">
            <h2 class="sqb-step-title"><?php echo esc_html__('Select Polishing Service', 'service-quote-builder'); ?></h2>
            <div class="sqb-grid sqb-polishing-grid">
                <div class="sqb-card sqb-card-poly" data-value="single" data-price="150" data-hours="7-10">
                    <div class="sqb-polish-badge"><?php echo esc_html__('Basic', 'service-quote-builder'); ?></div>
                    <h3><?php echo esc_html__('Single Stage Polish', 'service-quote-builder'); ?></h3>
                    <div class="sqb-hours"><?php echo esc_html__('7-10 hours', 'service-quote-builder'); ?></div>
                    <p class="sqb-poly-desc"><?php echo esc_html__('One-step polishing to remove minor defects and add gloss.', 'service-quote-builder'); ?></p>
                    <div class="sqb-price-tag"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>150<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></div>
                </div>
                <div class="sqb-card sqb-card-poly" data-value="enhancement" data-price="250" data-hours="13-19">
                    <div class="sqb-polish-badge sqb-badge-mid"><?php echo esc_html__('Popular', 'service-quote-builder'); ?></div>
                    <h3><?php echo esc_html__('Enhancement', 'service-quote-builder'); ?></h3>
                    <div class="sqb-hours"><?php echo esc_html__('13-19 hours', 'service-quote-builder'); ?></div>
                    <p class="sqb-poly-desc"><?php echo esc_html__('Two-stage polish for enhanced gloss and improved paint clarity.', 'service-quote-builder'); ?></p>
                    <div class="sqb-price-tag"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>250<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></div>
                </div>
                <div class="sqb-card sqb-card-poly" data-value="correction" data-price="400" data-hours="24-40">
                    <div class="sqb-polish-badge sqb-badge-premium"><?php echo esc_html__('Premium', 'service-quote-builder'); ?></div>
                    <h3><?php echo esc_html__('Full Correction', 'service-quote-builder'); ?></h3>
                    <div class="sqb-hours"><?php echo esc_html__('24-40 hours', 'service-quote-builder'); ?></div>
                    <p class="sqb-poly-desc"><?php echo esc_html__('Complete paint correction to remove swirl marks, scratches, and defects.', 'service-quote-builder'); ?></p>
                    <div class="sqb-price-tag"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>400<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></div>
                </div>
                <div class="sqb-card sqb-card-poly" data-value="wetsand" data-price="700" data-hours="75-113">
                    <div class="sqb-polish-badge sqb-badge-ultimate"><?php echo esc_html__('Ultimate', 'service-quote-builder'); ?></div>
                    <h3><?php echo esc_html__('Wet Sanding', 'service-quote-builder'); ?></h3>
                    <div class="sqb-hours"><?php echo esc_html__('75-113 hours', 'service-quote-builder'); ?></div>
                    <p class="sqb-poly-desc"><?php echo esc_html__('Advanced technique for removing deep scratches and restoring paint finish.', 'service-quote-builder'); ?></p>
                    <div class="sqb-price-tag"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>700<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></div>
                </div>
            </div>
            <input type="hidden" name="polishing" id="polishing" value="">
        </div>

        <!-- Step 4: Paint Protection Options -->
        <div class="sqb-step" data-step="4">
            <h2 class="sqb-step-title"><?php echo esc_html__('Choose Paint Protection', 'service-quote-builder'); ?></h2>
            <div class="sqb-grid sqb-protection-grid">
                <div class="sqb-card sqb-card-pro" data-value="c2" data-price="200" data-duration="6 months">
                    <div class="sqb-pro-header">
                        <h3>C2 Liquid Crystal</h3>
                        <span class="sqb-duration">6 <?php echo esc_html__('months', 'service-quote-builder'); ?></span>
                    </div>
                    <div class="sqb-ratings">
                        <div class="sqb-rating"><span><?php echo esc_html__('Durability', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 40%"></div></div></div>
                        <div class="sqb-rating"><span><?php echo esc_html__('Gloss', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 85%"></div></div></div>
                        <div class="sqb-rating"><span><?php echo esc_html__('Ease of Application', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 90%"></div></div></div>
                    </div>
                    <div class="sqb-price-tag"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>200<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></div>
                </div>
                <div class="sqb-card sqb-card-pro" data-value="exo" data-price="350" data-duration="18 months">
                    <div class="sqb-pro-header">
                        <h3>EXO</h3>
                        <span class="sqb-duration">18 <?php echo esc_html__('months', 'service-quote-builder'); ?></span>
                    </div>
                    <div class="sqb-ratings">
                        <div class="sqb-rating"><span><?php echo esc_html__('Durability', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 60%"></div></div></div>
                        <div class="sqb-rating"><span><?php echo esc_html__('Gloss', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 75%"></div></div></div>
                        <div class="sqb-rating"><span><?php echo esc_html__('Water Repellency', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 95%"></div></div></div>
                    </div>
                    <div class="sqb-price-tag"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>350<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></div>
                </div>
                <div class="sqb-card sqb-card-pro" data-value="csl" data-price="550" data-duration="5 years">
                    <div class="sqb-pro-header">
                        <h3>Crystal Serum Light</h3>
                        <span class="sqb-duration">5 <?php echo esc_html__('Year Guarantee', 'service-quote-builder'); ?></span>
                    </div>
                    <div class="sqb-ratings">
                        <div class="sqb-rating"><span><?php echo esc_html__('Durability', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 85%"></div></div></div>
                        <div class="sqb-rating"><span><?php echo esc_html__('Chemical Resistance', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 95%"></div></div></div>
                        <div class="sqb-rating"><span><?php echo esc_html__('Gloss', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 90%"></div></div></div>
                    </div>
                    <div class="sqb-price-tag"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>550<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></div>
                </div>
                <div class="sqb-card sqb-card-pro" data-value="csl-exo" data-price="750" data-duration="5 years">
                    <div class="sqb-pro-header">
                        <h3>CSL + EXO</h3>
                        <span class="sqb-duration">5 <?php echo esc_html__('Year Guarantee', 'service-quote-builder'); ?></span>
                    </div>
                    <div class="sqb-ratings">
                        <div class="sqb-rating"><span><?php echo esc_html__('Durability', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 95%"></div></div></div>
                        <div class="sqb-rating"><span><?php echo esc_html__('Gloss', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 98%"></div></div></div>
                        <div class="sqb-rating"><span><?php echo esc_html__('Wash Swirl Resistance', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 90%"></div></div></div>
                    </div>
                    <div class="sqb-price-tag"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>750<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></div>
                </div>
                <div class="sqb-card sqb-card-pro sqb-card-featured" data-value="csu" data-price="950" data-duration="9 years">
                    <div class="sqb-featured-badge"><?php echo esc_html__('Top Pick', 'service-quote-builder'); ?></div>
                    <div class="sqb-pro-header">
                        <h3>Crystal Serum Ultra</h3>
                        <span class="sqb-duration">9 <?php echo esc_html__('Year Guarantee', 'service-quote-builder'); ?></span>
                    </div>
                    <div class="sqb-ratings">
                        <div class="sqb-rating"><span><?php echo esc_html__('Durability', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 100%"></div></div></div>
                        <div class="sqb-rating"><span><?php echo esc_html__('Chemical Resistance', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 100%"></div></div></div>
                        <div class="sqb-rating"><span><?php echo esc_html__('UV Resistance', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 95%"></div></div></div>
                    </div>
                    <div class="sqb-price-tag"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>950<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></div>
                </div>
                <div class="sqb-card sqb-card-pro sqb-card-premium" data-value="csu-black" data-price="1200" data-duration="9 years">
                    <div class="sqb-premium-badge"><?php echo esc_html__('Premium Black', 'service-quote-builder'); ?></div>
                    <div class="sqb-pro-header">
                        <h3>CSU Black</h3>
                        <span class="sqb-duration">9 <?php echo esc_html__('Year Guarantee', 'service-quote-builder'); ?></span>
                    </div>
                    <div class="sqb-ratings">
                        <div class="sqb-rating"><span><?php echo esc_html__('Durability', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 100%"></div></div></div>
                        <div class="sqb-rating"><span><?php echo esc_html__('Gloss', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 100%"></div></div></div>
                        <div class="sqb-rating"><span><?php echo esc_html__('Dirt Repellency', 'service-quote-builder'); ?></span><div class="sqb-rating-bar"><div class="sqb-rating-fill" style="width: 98%"></div></div></div>
                    </div>
                    <div class="sqb-price-tag"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>1200<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></div>
                </div>
            </div>
            <input type="hidden" name="protection" id="protection" value="">
        </div>

        <!-- Step 5: Additional Protection (Checkboxes) -->
        <div class="sqb-step" data-step="5">
            <h2 class="sqb-step-title"><?php echo esc_html__('Additional Protection Services', 'service-quote-builder'); ?></h2>
            <p class="sqb-step-desc"><?php echo esc_html__('Select any additional services you would like to add:', 'service-quote-builder'); ?></p>
            <div class="sqb-grid sqb-extras-grid">
                <label class="sqb-checkbox-card">
                    <input type="checkbox" name="extra_glass" data-name="Windscreen & All Exterior Glass" data-price="80" data-hours="1">
                    <div class="sqb-checkbox-content">
                        <span class="sqb-checkbox-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <path d="M3 9h18M9 3v18"/>
                            </svg>
                        </span>
                        <span class="sqb-checkbox-label"><?php echo esc_html__('Windscreen & All Exterior Glass', 'service-quote-builder'); ?></span>
                        <span class="sqb-checkbox-price"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>80<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></span>
                        <span class="sqb-checkbox-hours">1 <?php echo esc_html__('hour', 'service-quote-builder'); ?></span>
                    </div>
                </label>
                <label class="sqb-checkbox-card">
                    <input type="checkbox" name="extra_rims" data-name="Rims" data-price="100" data-hours="2">
                    <div class="sqb-checkbox-content">
                        <span class="sqb-checkbox-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9"/>
                                <circle cx="12" cy="12" r="4"/>
                                <line x1="12" y1="3" x2="12" y2="8"/>
                                <line x1="12" y1="16" x2="12" y2="21"/>
                                <line x1="3" y1="12" x2="8" y2="12"/>
                                <line x1="16" y1="12" x2="21" y2="12"/>
                            </svg>
                        </span>
                        <span class="sqb-checkbox-label"><?php echo esc_html__('Rims', 'service-quote-builder'); ?></span>
                        <span class="sqb-checkbox-price"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>100<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></span>
                        <span class="sqb-checkbox-hours">2 <?php echo esc_html__('hours', 'service-quote-builder'); ?></span>
                    </div>
                </label>
                <label class="sqb-checkbox-card">
                    <input type="checkbox" name="extra_leather" data-name="Leather or Fabric Seats Including Alcantara" data-price="120" data-hours="1">
                    <div class="sqb-checkbox-content">
                        <span class="sqb-checkbox-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 18V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12"/>
                                <path d="M2 18h20"/>
                                <path d="M6 18v2"/>
                                <path d="M18 18v2"/>
                            </svg>
                        </span>
                        <span class="sqb-checkbox-label"><?php echo esc_html__('Leather or Fabric Seats Including Alcantara', 'service-quote-builder'); ?></span>
                        <span class="sqb-checkbox-price"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>120<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></span>
                        <span class="sqb-checkbox-hours">1 <?php echo esc_html__('hour', 'service-quote-builder'); ?></span>
                    </div>
                </label>
                <label class="sqb-checkbox-card">
                    <input type="checkbox" name="extra_interior" data-name="Dashboard, Door Cards, Carpets" data-price="90" data-hours="1">
                    <div class="sqb-checkbox-content">
                        <span class="sqb-checkbox-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="8" width="18" height="12" rx="2"/>
                                <path d="M7 8V6a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2"/>
                                <line x1="12" y1="12" x2="12" y2="16"/>
                            </svg>
                        </span>
                        <span class="sqb-checkbox-label"><?php echo esc_html__('Dashboard, Door Cards, Carpets', 'service-quote-builder'); ?></span>
                        <span class="sqb-checkbox-price"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>90<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></span>
                        <span class="sqb-checkbox-hours">1 <?php echo esc_html__('hour', 'service-quote-builder'); ?></span>
                    </div>
                </label>
                <label class="sqb-checkbox-card">
                    <input type="checkbox" name="extra_convertible" data-name="Convertible Roof Option" data-price="150" data-hours="1">
                    <div class="sqb-checkbox-content">
                        <span class="sqb-checkbox-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 17h18l-3-8H6l-3 8z"/>
                                <path d="M6 9V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v3"/>
                                <circle cx="7" cy="19" r="2"/>
                                <circle cx="17" cy="19" r="2"/>
                            </svg>
                        </span>
                        <span class="sqb-checkbox-label"><?php echo esc_html__('Convertible Roof Option', 'service-quote-builder'); ?></span>
                        <span class="sqb-checkbox-price"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>150<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></span>
                        <span class="sqb-checkbox-hours">1 <?php echo esc_html__('hour', 'service-quote-builder'); ?></span>
                    </div>
                </label>
                <label class="sqb-checkbox-card">
                    <input type="checkbox" name="extra_bundle" data-name="Total Surface Protection Bundle" data-price="200" data-hours="5">
                    <div class="sqb-checkbox-content">
                        <span class="sqb-checkbox-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                <path d="M2 17l10 5 10-5"/>
                                <path d="M2 12l10 5 10-5"/>
                            </svg>
                        </span>
                        <span class="sqb-checkbox-label"><?php echo esc_html__('Total Surface Protection Bundle', 'service-quote-builder'); ?></span>
                        <span class="sqb-checkbox-price"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>200<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></span>
                        <span class="sqb-checkbox-hours">5 <?php echo esc_html__('hours', 'service-quote-builder'); ?></span>
                    </div>
                </label>
            </div>

            <!-- Maintenance Kits -->
            <h3 class="sqb-sub-title"><?php echo esc_html__('Maintenance Kits (Optional Add-ons)', 'service-quote-builder'); ?></h3>
            <div class="sqb-grid sqb-kits-grid">
                <label class="sqb-checkbox-card sqb-kit-card">
                    <input type="checkbox" name="kit_essential" data-name="Essential Maintenance Kit" data-price="80">
                    <div class="sqb-checkbox-content">
                        <span class="sqb-checkbox-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                            </svg>
                        </span>
                        <span class="sqb-checkbox-label"><?php echo esc_html__('Essential Maintenance Kit', 'service-quote-builder'); ?></span>
                        <span class="sqb-checkbox-price"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>80<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></span>
                    </div>
                </label>
                <label class="sqb-checkbox-card sqb-kit-card">
                    <input type="checkbox" name="kit_basic" data-name="Basic Maintenance Kit" data-price="120">
                    <div class="sqb-checkbox-content">
                        <span class="sqb-checkbox-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <path d="M9 9h6v6H9z"/>
                            </svg>
                        </span>
                        <span class="sqb-checkbox-label"><?php echo esc_html__('Basic Maintenance Kit', 'service-quote-builder'); ?></span>
                        <span class="sqb-checkbox-price"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>120<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Step 6: Customer Details Form -->
        <div class="sqb-step" data-step="6">
            <h2 class="sqb-step-title"><?php echo esc_html__('Complete Your Reservation', 'service-quote-builder'); ?></h2>
            <div class="sqb-form-grid">
                <div class="sqb-form-group">
                    <label for="customer_name"><?php echo esc_html__('Full Name', 'service-quote-builder'); ?> *</label>
                    <input type="text" id="customer_name" name="customer_name" required>
                </div>
                <div class="sqb-form-group">
                    <label for="customer_email"><?php echo esc_html__('Email Address', 'service-quote-builder'); ?> *</label>
                    <input type="email" id="customer_email" name="customer_email" required>
                </div>
                <div class="sqb-form-group">
                    <label for="customer_phone"><?php echo esc_html__('Contact Number', 'service-quote-builder'); ?> *</label>
                    <input type="tel" id="customer_phone" name="customer_phone" required>
                </div>
                <div class="sqb-form-group">
                    <label for="vehicle_make"><?php echo esc_html__('Vehicle Make', 'service-quote-builder'); ?> *</label>
                    <input type="text" id="vehicle_make" name="vehicle_make" placeholder="e.g., BMW, Mercedes, Tesla" required>
                </div>
                <div class="sqb-form-group">
                    <label for="vehicle_model"><?php echo esc_html__('Vehicle Model', 'service-quote-builder'); ?> *</label>
                    <input type="text" id="vehicle_model" name="vehicle_model" placeholder="e.g., M3, E-Class, Model S" required>
                </div>
                <div class="sqb-form-group">
                    <label for="preferred_date"><?php echo esc_html__('Preferred Service Date', 'service-quote-builder'); ?></label>
                    <input type="date" id="preferred_date" name="preferred_date">
                </div>
                <div class="sqb-form-group sqb-form-full">
                    <label for="address"><?php echo esc_html__('Full Address', 'service-quote-builder'); ?></label>
                    <textarea id="address" name="address" rows="3"></textarea>
                </div>
                <div class="sqb-form-group sqb-form-full">
                    <label for="country"><?php echo esc_html__('Country/Region', 'service-quote-builder'); ?></label>
                    <select id="country" name="country">
                        <option value=""><?php echo esc_html__('Select country...', 'service-quote-builder'); ?></option>
                        <option value="US">United States</option>
                        <option value="UK">United Kingdom</option>
                        <option value="CA">Canada</option>
                        <option value="AU">Australia</option>
                        <option value="DE">Germany</option>
                        <option value="FR">France</option>
                        <option value="NL">Netherlands</option>
                        <option value="BE">Belgium</option>
                        <option value="IE">Ireland</option>
                        <option value="SE">Sweden</option>
                        <option value="NO">Norway</option>
                        <option value="DK">Denmark</option>
                        <option value="FI">Finland</option>
                        <option value="IT">Italy</option>
                        <option value="ES">Spain</option>
                        <option value="PT">Portugal</option>
                        <option value="PL">Poland</option>
                        <option value="AT">Austria</option>
                        <option value="CH">Switzerland</option>
                        <option value="JP">Japan</option>
                        <option value="Other"><?php echo esc_html__('Other', 'service-quote-builder'); ?></option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="sqb-navigation">
            <button type="button" class="sqb-btn sqb-btn-secondary" id="sqb-prev" style="display: none;">
                <?php echo esc_html__('← Previous', 'service-quote-builder'); ?>
            </button>
            <button type="button" class="sqb-btn sqb-btn-primary" id="sqb-next">
                <?php echo esc_html__('Next →', 'service-quote-builder'); ?>
            </button>
            <button type="submit" class="sqb-btn sqb-btn-submit" id="sqb-submit" style="display: none;">
                <?php echo esc_html__('Submit Quote Request', 'service-quote-builder'); ?>
            </button>
        </div>
    </form>

    <!-- Running Total Sidebar -->
    <div class="sqb-sidebar">
        <div class="sqb-totals">
            <h3><?php echo esc_html__('Your Quote', 'service-quote-builder'); ?></h3>
            <div class="sqb-total-items" id="sqb-total-items">
                <!-- Dynamic items will be inserted here -->
            </div>
            <div class="sqb-total-row sqb-subtotal">
                <span><?php echo esc_html__('Subtotal', 'service-quote-builder'); ?></span>
                <span id="sqb-subtotal"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>0.00<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></span>
            </div>
            <?php if ($vat_rate > 0): ?>
            <div class="sqb-total-row sqb-vat">
                <span><?php echo esc_html__('VAT', 'service-quote-builder'); ?> (<?php echo esc_html($vat_rate); ?>%)</span>
                <span id="sqb-vat"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>0.00<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></span>
            </div>
            <?php endif; ?>
            <div class="sqb-total-row sqb-grand-total">
                <span><?php echo esc_html__('Total *', 'service-quote-builder'); ?></span>
                <span id="sqb-grand-total"><?php echo $currency_position === 'before' ? esc_html($currency_symbol) : ''; ?>0.00<?php echo $currency_position === 'after' ? esc_html($currency_symbol) : ''; ?></span>
            </div>
            <p class="sqb-disclaimer">* <?php echo esc_html__('All estimates require a visual inspection of your vehicle before a final quote can be made.', 'service-quote-builder'); ?></p>
        </div>
        <div class="sqb-actions">
            <button type="button" class="sqb-btn sqb-btn-save" id="sqb-save-cart">
                <?php echo esc_html__('Save Cart', 'service-quote-builder'); ?>
            </button>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="sqb-modal" id="sqb-success-modal" style="display: none;">
    <div class="sqb-modal-content">
        <span class="sqb-modal-close">&times;</span>
        <div class="sqb-modal-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <h2><?php echo esc_html__('Thank You!', 'service-quote-builder'); ?></h2>
        <p><?php echo esc_html__('Your quote request has been submitted successfully.', 'service-quote-builder'); ?></p>
        <p class="sqb-modal-message"></p>
        <button type="button" class="sqb-btn sqb-btn-primary sqb-modal-btn"><?php echo esc_html__('Start New Quote', 'service-quote-builder'); ?></button>
    </div>
</div>

<!-- Save Cart Modal -->
<div class="sqb-modal" id="sqb-save-modal" style="display: none;">
    <div class="sqb-modal-content sqb-modal-small">
        <span class="sqb-modal-close">&times;</span>
        <h2><?php echo esc_html__('Save Your Quote', 'service-quote-builder'); ?></h2>
        <p><?php echo esc_html__('Enter your email to save this quote and get a shareable link:', 'service-quote-builder'); ?></p>
        <div class="sqb-form-group">
            <label for="save_email"><?php echo esc_html__('Email Address', 'service-quote-builder'); ?></label>
            <input type="email" id="save_email" name="save_email" required>
        </div>
        <button type="button" class="sqb-btn sqb-btn-primary" id="sqb-confirm-save"><?php echo esc_html__('Save Quote', 'service-quote-builder'); ?></button>
    </div>
</div>