<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/functions.php';

// Lấy dữ liệu từ database
$slides = get_all_slides();
$tin_tuc = get_posts_by_category('tin-tuc', 5);
$su_kien = get_posts_by_category('su-kien', 5);
$huong_dan = get_posts_by_category('huong-dan', 5);
?>
<!DOCTYPE html>
<html lang="vi" class="__roots root__page">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= get_setting('site_name') ?></title>
    <link rel="shortcut icon" type="ico" href="/favicon.ico" />
    
    <meta name="description" content="<?= get_setting('site_description') ?>" />
    <meta name="keywords" content="<?= get_setting('site_keywords') ?>" />
    
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?= get_setting('site_name') ?>" />
    <meta property="og:description" content="<?= get_setting('site_description') ?>" />
    <meta property="og:site_name" content="<?= get_setting('site_name') ?>" />
    <meta property="og:image" content="/assets/frontend/teaser/images/thumb.jpg" />
    
    <link rel="stylesheet" href="/assets/frontend/home/v1/css/slick-theme.css" />
    <link rel="stylesheet" href="/assets/frontend/home/v1/css/slick.css" />
    <link rel="stylesheet" href="/assets/frontend/home/v1/css/jquery.fancybox.min.css" />
    <link rel="stylesheet" href="/assets/frontend/home/v1/css/aos.css" />
    <link rel="stylesheet" href="/assets/frontend/home/v1/css/stylea6ca.css?v=919" />
    <link rel="stylesheet" href="/assets/css/auth.css" />
    
    <style>
        @font-face {
            font-family: 'Bangers';
            src: url('/assets/frontend/home/v1/fonts/Bangers-Regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        
        #menu li a {
            font-family: 'Bangers', cursive !important;
            font-size: 20px;
            letter-spacing: 1px;
        }

        .bg_video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        #videoBgPC {
            width: 100%;
            height: 100%;
            object-fit: cover;
            left: 0 !important;
            top: 0 !important;
            transform: none !important;
        }

        .game--brand__show {
            position: relative;
            overflow: hidden;
            min-height: 800px;
        }

        @media only screen and (max-width: 1023px) {
            .game--brand__show {
                min-height: 500px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <section class="__section main_head __zero">
        <input id="toggle-menu__header-page" type="checkbox" style="display: none" />
        
        <div class="navbar">
            <div class="limit__game">
                <!-- <a href="index.php" class="hidden__mobile">
                    <img src="assets/frontend/home/v1/images/rtsc.png" alt="" class="logo-top" />
                </a> -->
                
                <div class="left-header hidden__PC">
                    <div class="icon-name-game dFlex">
                        <div class="icon-game">
                            <img src="assets/frontend/home/v1/images/bannergame.png" alt="" />
                        </div>
                        <div class="txt-name-game c-white">
                            <div class="name-game f-sVN-Avengeance"><?= get_setting('site_name') ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="navbar-content tCenter">
                    <ul id="menu" class="f-Roboto-Regular" style="text-align: center;">
                        <!-- <li><a href="index.php" class="">Trang chủ</a></li> -->
                        <li style="text-align: center;"><a href="bangxephang.php" class="" style="display: block; text-align: center;">BXH</a></li>
                        <li style="text-align: center;"><a href="/tin-tuc" class="" style="display: block; text-align: center;">Tin tức</a></li>
                        <li style="text-align: center;"><a href="/su-kien" class="" style="display: block; text-align: center;">Sự kiện</a></li>
                        <li style="text-align: center;"><a href="/huong-dan" class="" style="display: block; text-align: center;">Hướng dẫn</a></li>
                       <!-- <li><a target="_blank" href="<?= get_setting('facebook_url') ?>" class="">Fanpage</a></li>
                         -->
                        <?php if (is_logged_in()): ?>
                            <li class="user-menu" style="text-align: center;">
                                <a href="#" style="background: transparent; display: block; text-align: center;"><?= escape(get_logged_in_user()['username']) ?></a>
                                <ul class="dropdown" style="background: transparent; border: none; box-shadow: none; margin-top: 5px;">
                                    <li style="text-align: center;"><a href="/profile" style="background: transparent; display: flex; align-items: center; justify-content: center; height: 50px;">Hồ sơ</a></li>
                                    <?php if (is_admin()): ?>
                                        <li style="text-align: center;"><a href="admin/" style="background: transparent; display: flex; align-items: center; justify-content: center; height: 50px;">Quản trị</a></li>
                                    <?php endif; ?>
                                    <li style="text-align: center;"><a href="/logout" style="background: transparent; display: flex; align-items: center; justify-content: center; height: 50px;">Đăng xuất</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li style="text-align: center;"><a href="/login" style="background: transparent; display: block; text-align: center;">Đăng nhập</a></li>
                            <li style="text-align: center;"><a href="/register" style="background: transparent; display: block; text-align: center;">Đăng ký</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="icon-hamburger hidden__PC">
                    <label for="toggle-menu__header-page" id="menu__header-page">
                        <div class="inner-menu__header-page"></div>
                    </label>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Hero Section -->
    <section class="__section game--brand__show __1">
        <div class="bg_video">
            <video id="videoBgPC" class="videobg hidden__mobile" muted="" loop="" preload="none" webkit-playsinline="" playsinline="">
                <source src="assets/frontend/teaser/videos/g.mp4" type="video/mp4" />
            </video>
        </div>
        <div class="limit__game">
            <div class="main--game__show">
                <div class="text--brand t-center m-auto p-relative" data-aos="fade-down" data-aos-duration="700" data-aos-delay="100">
                    <img src="assets/frontend/home/v1/images/textgame.png" alt="" class="textgame__game" />
                </div>
            </div>
            
            <div class="box--download jCenter">
                <div class="list-link-dl">
                    <a target="_blank" href="<?= get_setting('ios_download_url') ?>" class="item-link link-apple">
                        <img class="img-ac" src="assets/frontend/home/v1/images/btn-dl/btn-dl.png" alt="" />
                        <img class="img-hv" src="assets/frontend/home/v1/images/btn-dl/btn-dl-hv.png" alt="" />
                    </a>
                    
                    <a href="<?= get_setting('android_download_url') ?>" class="item-link link-android">
                        <img class="img-ac" src="assets/frontend/home/v1/images/btn-dl/btn-dl-android.png" alt="" />
                        <img class="img-hv" src="assets/frontend/home/v1/images/btn-dl/btn-dl-android-hv.png" alt="" />
                    </a>
                    
                    <a target="_blank" href="<?= get_setting('apk_download_url') ?>" class="item-link link-android">
                        <img class="img-ac" src="assets/frontend/home/v1/images/btn-dl/btn-dl-apk.png" alt="" />
                        <img class="img-hv" src="assets/frontend/home/v1/images/btn-dl/btn-dl-apk-hv.png" alt="" />
                    </a>
                    
                    <a target="_blank" href="<?= get_setting('payment_url') ?>" class="item-link link-card">
                        <img class="img-ac" src="assets/frontend/home/v1/images/btn-dl/btn-card.png" alt="" />
                        <img class="img-hv" src="assets/frontend/home/v1/images/btn-dl/btn-card-hv.png" alt="" />
                    </a>
                    
                    <a target="_blank" href="<?= get_setting('facebook_url') ?>" class="item-link link-fb">
                        <img class="img-ac" src="assets/frontend/home/v1/images/btn-dl/btn-fb.png" alt="" />
                        <img class="img-hv" src="assets/frontend/home/v1/images/btn-dl/btn-fb-hv.png" alt="" />
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Content Section -->
    <div class="box--content">
        <section class="__section box__new __2 clearfix">
            <div class="tit-frame tCenter">
                <img src="assets/frontend/home/v1/images/ttsk.png" style="width: 60%; max-width: 411px" />
            </div>
            <div class="limit__game">
                <div class="main--box__new" data-aos="fade-up" data-aos-duration="700" data-aos-delay="400">
                    <div class="list-slide box-border p-r">
                        <div class="listSlide__new">
                            <?php foreach ($slides as $slide): ?>
                                <a href="<?= escape($slide['link']) ?>" target="_blank">
                                    <img src="<?= escape($slide['image']) ?>" alt="<?= escape($slide['title']) ?>">
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <div class="icon-rau rau-left-top"></div>
                        <div class="icon-rau rau-right-bottom"></div>
                    </div>
                    
                    <div class="box-list-new box-border p-r">
                        <div class="tab-new clearfix f-utm_facebook">
                            <div class="tab-link custom-border current" data-tab="tab-tin-tuc" data-more="viewtin-tuc">
                                <span>Tin tức</span>
                            </div>
                            <div class="tab-link custom-border" data-tab="tab-su-kien" data-more="viewsu-kien">
                                <span>Sự kiện</span>
                            </div>
                            <div class="tab-link custom-border" data-tab="tab-huong-dan" data-more="viewhuong-dan">
                                <span>Hướng dẫn</span>
                            </div>
                        </div>
                        
                        <div class="tab-content">
                            <div class="tab-detail current" id="tab-tin-tuc">
                                <?php foreach ($tin_tuc as $post): ?>
                                    <a href="/post/<?= escape($post['slug']) ?>" class="item-new-box f-Roboto-Regular">
                                        <div class="cat-des"><?= escape($post['title']) ?></div>
                                        <div class="date-open"><?= format_date($post['created_at']) ?></div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="tab-detail" id="tab-su-kien">
                                <?php foreach ($su_kien as $post): ?>
                                    <a href="/post/<?= escape($post['slug']) ?>" class="item-new-box f-Roboto-Regular">
                                        <div class="cat-des"><?= escape($post['title']) ?></div>
                                        <div class="date-open"><?= format_date($post['created_at']) ?></div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="tab-detail" id="tab-huong-dan">
                                <?php foreach ($huong_dan as $post): ?>
                                    <a href="/post/<?= escape($post['slug']) ?>" class="item-new-box f-Roboto-Regular">
                                        <div class="cat-des"><?= escape($post['title']) ?></div>
                                        <div class="date-open"><?= format_date($post['created_at']) ?></div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="view-more">
                            <a href="/" id="viewtin-tuc" class="events a100 link-more current"></a>
                            <a href="/" id="viewsu-kien" class="events a100 link-more"></a>
                            <a href="/" id="viewhuong-dan" class="events a100 link-more"></a>
                        </div>
                        
                        <div class="icon-rau rau-left-bottom"></div>
                        <div class="icon-rau rau-right-top"></div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Quick Links -->
        <div class="box-link">
            <div class="container">
                <div class="main-box-link">
                    <a href="<?= get_setting('facebook_group_url') ?>" class="item-box-link" data-aos="fade-up" data-aos-duration="700" data-aos-delay="400">
                        <img class="img-ac" src="assets/frontend/home/v1/images/box-link/img-gr.png" alt="" />
                        <img class="img-hv" src="assets/frontend/home/v1/images/box-link/img-gr-hv.png" alt="" />
                    </a>
                    
                    <a href="<?= get_setting('facebook_url') ?>" class="item-box-link hidden-mobile" data-aos="fade-up" data-aos-duration="700" data-aos-delay="600">
                        <img class="img-ac" src="assets/frontend/home/v1/images/box-link/img-fb.png" alt="" />
                        <img class="img-hv" src="assets/frontend/home/v1/images/box-link/img-fb-hv.png" alt="" />
                    </a>
                    
                    <a target="_blank" href="giftcode.php" class="item-box-link" data-aos="fade-up" data-aos-duration="700" data-aos-delay="800">
                        <img class="img-ac" src="assets/frontend/home/v1/images/box-link/img-gc.png" alt="" />
                        <img class="img-hv" src="assets/frontend/home/v1/images/box-link/img-gc-hv.png" alt="" />
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Game Features -->
    <section class="__section box_game ftg__sl __3">
        <div class="limit__game">
            <div class="tit-frame tCenter">
                <img src="/assets/frontend/teaser/images/ten_box_game/tit-tinhnang.png" style="width: 60%; max-width: 411px" />
            </div>
            
            <div class="bg__sl_ft p-r m__inline">
                <img src="/assets/frontend/teaser/images/ftgame/bg-tn.png" style="width: 100%" />
                <div class="slide__tinhnang slide__feature p-a slick-custom-dots">
                    <img src="/assets/frontend/teaser/images/ftgame/teaser1.jpg" />
                    <img src="/assets/frontend/teaser/images/ftgame/teaser2.jpg" />
                    <img src="/assets/frontend/teaser/images/ftgame/teaser3.jpg" />
                    <img src="/assets/frontend/teaser/images/ftgame/teaser4.jpg" />
                    <img src="/assets/frontend/teaser/images/ftgame/teaser5.jpg" />
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script type="text/javascript" src="/assets/frontend/home/v1/js/jquery.min.js"></script>
    <script type="text/javascript" src="/assets/frontend/home/v1/js/ScrollMagic.min.js"></script>
    <script type="text/javascript" src="/assets/frontend/home/v1/js/aos.js"></script>
    <script type="text/javascript" src="/assets/frontend/home/v1/js/slick.min.js"></script>
    <script type="text/javascript" src="/assets/frontend/home/v1/js/jquery.fancybox.min.js"></script>
    <script type="text/javascript" src="/assets/js/main.js"></script>
</body>
</html>
