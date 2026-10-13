<div id="page-content" class="page-wrapper clearfix help-page-container <?php echo "sub_" . $type; ?>">

    <div class="view-container">

        <div class="row">

            <!-- ==========================================
                 LEFT SIDEBAR - ENHANCED
            =========================================== -->

            <div id="help-left-panel" class="col-md-3">

                <!-- Search Box -->
                <?php echo view("help_and_knowledge_base/search_box", array("type" => $type)); ?>

                <!-- Categories - Modern Sidebar -->
                <div class="category-card-modern">

                    <div class="category-header-modern">
                        <div class="header-icon-wrapper">
                            <i data-feather="book-open" class="header-icon"></i>
                        </div>
                        <span class="header-title"><?php echo app_lang("categories"); ?></span>
                        <span class="header-badge"><?php echo count($categories); ?></span>
                    </div>

                    <ul class="list-group help-category-list-modern">

                        <?php
                        foreach ($categories as $category) {

                            $active_class = "";

                            if ($category->id === $selected_category_id) {
                                $active_class = "active";
                            }

                            $icon = "file-text";

                            $title = strtolower($category->title);

                            if (strpos($title, "login") !== false) {
                                $icon = "shield";
                            } elseif (strpos($title, "device") !== false) {
                                $icon = "cpu";
                            } elseif (strpos($title, "model") !== false) {
                                $icon = "layers";
                            } elseif (strpos($title, "product") !== false) {
                                $icon = "package";
                            } elseif (strpos($title, "data") !== false) {
                                $icon = "file-text";
                            } elseif (strpos($title, "catalog") !== false) {
                                $icon = "book-open";
                            }

                            echo anchor(

                                get_uri($type . "/category/" . $category->id),

                                "
                                <div class='category-item-modern " . ($active_class ? "active" : "") . "'>
                                    <div class='category-icon-wrapper'>
                                        <i data-feather='{$icon}' class='category-icon-modern'></i>
                                    </div>
                                    <span class='category-text-modern'>
                                        {$category->title}
                                    </span>
                                    <span class='category-arrow-modern'>
                                        <i data-feather='chevron-right'></i>
                                    </span>
                                </div>
                                ",

                                array(
                                    "class" => "list-group-item-modern " . $active_class,
                                    "escape" => false
                                )

                            );
                        }
                        ?>

                    </ul>

                </div>

            </div>

            <!-- ==========================================
                 RIGHT CONTENT
            =========================================== -->

            <div class="col-md-9">

                <div id="help-page-content" class="card-modern">

                    <!-- ==========================================
                         BACK BUTTON - Arrow only at top right
                    =========================================== -->
                    <?php if ($page_type == "article_view" || $page_type == "articles_list_view"): ?>
                    <div class="content-header-modern">
                        <?php if ($page_type == "article_view" && isset($article_info->title)): ?>
                        <div class="article-title-wrapper">
                            <span class="article-title-text"><?php echo $article_info->title; ?></span>
                        </div>
                        <?php elseif ($page_type == "articles_list_view" && isset($category_info->title)): ?>
                        <div class="article-title-wrapper">
                            <span class="article-title-text"><?php echo $category_info->title; ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="back-button-wrapper">
                            <a href="<?php echo get_uri($type); ?>" class="back-button-modern" title="Back to Categories">
                                <i data-feather="arrow-left" class="back-icon"></i>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- ==========================================
                         MAIN CONTENT WITH BOTTOM PADDING
                    =========================================== -->
                    <div class="content-body-modern">
                        <?php

                        if ($page_type == "articles_list_view") {

                            echo view(
                                "help_and_knowledge_base/articles/articles_list_view",
                                array(
                                    "category_info" => $category_info,
                                    "articles" => $articles
                                )
                            );

                        } else if ($page_type == "article_view") {

                            echo view(
                                "help_and_knowledge_base/articles/article_view",
                                array(
                                    "article_info" => $article_info
                                )
                            );

                        }

                        ?>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- ==========================================
     ENHANCED STYLES
=========================================== -->
<style>
/* ==========================================
   MODERN SIDEBAR STYLES
========================================== */

/* Container */
.help-page-container {
    background: #f0f4f8;
    padding: 20px 0 40px 0;
    min-height: 100vh;
}

.view-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

/* ==========================================
   SEARCH BOX ENHANCEMENT
========================================== */
#help-left-panel .search-box {
    margin-bottom: 20px;
}

#help-left-panel .search-box .input-group {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

#help-left-panel .search-box .input-group:focus-within {
    border-color: #4e73df;
    box-shadow: 0 0 0 4px rgba(78, 115, 223, 0.1);
}

#help-left-panel .search-box .input-group .form-control {
    border: none;
    padding: 12px 16px;
    font-size: 0.95rem;
    background: transparent;
    border-radius: 12px 0 0 12px;
}

#help-left-panel .search-box .input-group .form-control:focus {
    box-shadow: none;
}

#help-left-panel .search-box .input-group .input-group-btn .btn {
    background: transparent;
    border: none;
    padding: 12px 16px;
    color: #4e73df;
}

/* ==========================================
   CATEGORY CARD - MODERN
========================================== */
.category-card-modern {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid #e8ecf1;
    overflow: hidden;
    transition: all 0.3s ease;
}

.category-card-modern:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}

/* Header */
.category-header-modern {
    padding: 18px 24px;
    background: linear-gradient(135deg, #f8faff 0%, #f0f5ff 100%);
    border-bottom: 1px solid #e8ecf1;
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-icon-wrapper {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.header-icon-wrapper .header-icon {
    width: 18px;
    height: 18px;
    color: #ffffff;
}

.header-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1a2332;
    flex: 1;
}

.header-badge {
    background: rgba(78, 115, 223, 0.1);
    color: #4e73df;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 2px 12px;
    border-radius: 50px;
}

/* Category List */
.help-category-list-modern {
    list-style: none;
    padding: 8px 0;
    margin: 0;
}

.list-group-item-modern {
    display: block;
    padding: 0;
    margin: 0;
    border: none;
    background: transparent;
    text-decoration: none;
    transition: all 0.2s ease;
}

.list-group-item-modern:hover {
    background: transparent;
}

.list-group-item-modern .category-item-modern {
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    cursor: pointer;
    transition: all 0.25s ease;
    position: relative;
    border-left: 3px solid transparent;
}

.list-group-item-modern .category-item-modern::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 0;
    background: rgba(78, 115, 223, 0.04);
    transition: width 0.3s ease;
    pointer-events: none;
}

.list-group-item-modern:hover .category-item-modern::after {
    width: 100%;
}

/* Icon Wrapper */
.category-icon-wrapper {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: rgba(78, 115, 223, 0.08);
    transition: all 0.3s ease;
}

.category-icon-modern {
    width: 16px;
    height: 16px;
    color: #4e73df;
    transition: all 0.3s ease;
}

.list-group-item-modern:hover .category-icon-wrapper {
    background: rgba(78, 115, 223, 0.15);
    transform: scale(1.05);
}

.list-group-item-modern:hover .category-icon-modern {
    color: #224abe;
}

/* Text */
.category-text-modern {
    flex: 1;
    font-size: 0.92rem;
    font-weight: 500;
    color: #475569;
    transition: all 0.3s ease;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.list-group-item-modern:hover .category-text-modern {
    color: #1a2332;
}

/* Arrow */
.category-arrow-modern {
    opacity: 0;
    transform: translateX(-8px);
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.category-arrow-modern i {
    width: 16px;
    height: 16px;
    color: #94a3b8;
}

.list-group-item-modern:hover .category-arrow-modern {
    opacity: 1;
    transform: translateX(0);
}

/* Active State */
.list-group-item-modern.active .category-item-modern {
    background: rgba(78, 115, 223, 0.06);
    border-left-color: #4e73df;
}

.list-group-item-modern.active .category-icon-wrapper {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
}

.list-group-item-modern.active .category-icon-modern {
    color: #ffffff;
}

.list-group-item-modern.active .category-text-modern {
    color: #1a2332;
    font-weight: 600;
}

.list-group-item-modern.active .category-arrow-modern {
    opacity: 1;
    transform: translateX(0);
}

.list-group-item-modern.active .category-arrow-modern i {
    color: #4e73df;
}

/* ==========================================
   RIGHT CONTENT - MODERN CARD
========================================== */
.card-modern {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid #e8ecf1;
    overflow: hidden;
    min-height: 500px;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.card-modern:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
}

/* ==========================================
   CONTENT HEADER WITH BACK BUTTON (Arrow Only)
========================================== */
.content-header-modern {
    padding: 12px 20px;
    background: #fafbff;
    border-bottom: 1px solid #e8ecf1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-shrink: 0;
}

.back-button-wrapper {
    flex-shrink: 0;
}

.back-button-modern {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    color: #475569;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
}

.back-button-modern:hover {
    background: #4e73df;
    border-color: #4e73df;
    color: #ffffff;
    transform: translateX(-3px);
    box-shadow: 0 4px 12px rgba(78, 115, 223, 0.25);
}

.back-button-modern .back-icon {
    width: 20px;
    height: 20px;
    transition: all 0.3s ease;
}

.back-button-modern:hover .back-icon {
    color: #ffffff;
    transform: translateX(-2px);
}

.article-title-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
    justify-content: center;
}

.article-title-wrapper .article-title-text {
    font-size: 1rem;
    font-weight: 600;
    color: #1a2332;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ==========================================
   CONTENT BODY - WITH BOTTOM PADDING
========================================== */
.content-body-modern {
    flex: 1;
    padding: 24px 24px 60px 24px; /* Added 60px bottom padding */
    overflow: auto;
}

/* ==========================================
   RESPONSIVE
========================================== */
@media (max-width: 992px) {
    #help-left-panel {
        margin-bottom: 24px;
    }
    
    .category-text-modern {
        font-size: 0.85rem;
    }
    
    .category-item-modern {
        padding: 10px 16px !important;
    }
}

@media (max-width: 768px) {
    .view-container {
        padding: 0 12px;
    }
    
    .category-header-modern {
        padding: 14px 18px;
    }
    
    .header-title {
        font-size: 0.9rem;
    }
    
    .category-item-modern {
        padding: 10px 14px !important;
        gap: 10px;
    }
    
    .category-icon-wrapper {
        width: 30px;
        height: 30px;
    }
    
    .category-icon-modern {
        width: 14px;
        height: 14px;
    }
    
    .category-text-modern {
        font-size: 0.82rem;
    }
    
    .card-modern {
        padding: 0;
        min-height: 300px;
    }
    
    .content-header-modern {
        padding: 10px 14px;
    }
    
    .back-button-modern {
        width: 32px;
        height: 32px;
    }
    
    .back-button-modern .back-icon {
        width: 16px;
        height: 16px;
    }
    
    .article-title-wrapper .article-title-text {
        font-size: 0.9rem;
    }
    
    .content-body-modern {
        padding: 16px 16px 40px 16px; /* Adjusted for mobile */
    }
}

@media (max-width: 480px) {
    .category-item-modern {
        padding: 8px 12px !important;
        gap: 8px;
    }
    
    .category-icon-wrapper {
        width: 26px;
        height: 26px;
    }
    
    .category-icon-modern {
        width: 12px;
        height: 12px;
    }
    
    .category-text-modern {
        font-size: 0.78rem;
    }
    
    .header-title {
        font-size: 0.8rem;
    }
    
    .header-badge {
        font-size: 0.65rem;
        padding: 1px 10px;
    }
    
    .card-modern {
        padding: 0;
        min-height: 200px;
    }
    
    .content-header-modern {
        padding: 8px 10px;
    }
    
    .back-button-modern {
        width: 28px;
        height: 28px;
        border-radius: 8px;
    }
    
    .back-button-modern .back-icon {
        width: 14px;
        height: 14px;
    }
    
    .article-title-wrapper .article-title-text {
        font-size: 0.78rem;
    }
    
    .content-body-modern {
        padding: 12px 12px 30px 12px; /* Adjusted for mobile */
    }
}

/* ==========================================
   ANIMATIONS
========================================== */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.category-item-modern {
    animation: slideIn 0.3s ease both;
}

.category-item-modern:nth-child(1) { animation-delay: 0.05s; }
.category-item-modern:nth-child(2) { animation-delay: 0.1s; }
.category-item-modern:nth-child(3) { animation-delay: 0.15s; }
.category-item-modern:nth-child(4) { animation-delay: 0.2s; }
.category-item-modern:nth-child(5) { animation-delay: 0.25s; }
.category-item-modern:nth-child(6) { animation-delay: 0.3s; }
.category-item-modern:nth-child(7) { animation-delay: 0.35s; }
.category-item-modern:nth-child(8) { animation-delay: 0.4s; }

/* ==========================================
   SCROLLBAR STYLING
========================================== */
.help-category-list-modern::-webkit-scrollbar {
    width: 4px;
}

.help-category-list-modern::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.help-category-list-modern::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.help-category-list-modern::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* ==========================================
   TOOLTIP FOR LONG CATEGORY NAMES
========================================== */
.category-text-modern {
    position: relative;
}

.category-text-modern:hover::after {
    content: attr(data-fulltext);
    position: absolute;
    bottom: 100%;
    left: 0;
    background: #1a2332;
    color: #ffffff;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 100;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    pointer-events: none;
}
</style>

<script type="text/javascript">

$(document).ready(function () {

    /* Minimum Height */
    $("#help-page-content").css({
        "min-height": ($(window).height() - 110) + "px"
    });

    /* Feather Icons */
    if (typeof feather !== "undefined") {
        feather.replace();
    }

    /* Mobile Scroll */
    <?php if (isset($scroll_to_content)) { ?>
    if (isMobile()) {
        setTimeout(function () {
            window.scrollTo(
                0,
                $("#help-page-content").offset().top - 60
            );
        }, 200);
    }
    <?php } ?>

    /* Add data-fulltext attribute for tooltips */
    $('.category-text-modern').each(function() {
        var text = $(this).text().trim();
        $(this).attr('data-fulltext', text);
    });

});

</script>