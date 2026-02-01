<div class="footer-ace f-tahoma footer__game __6 tUpper p-r">
    <div class="link-other dFlex aCenter jCenter">
        <a href="<?= get_setting('facebook_url') ?>" title="" class=" " target="_blank">
            <img src="/assets/frontend/teaser/images/footer_game/img-fp.png" alt="">
        </a>
        <a href="<?= get_setting('facebook_group_url') ?>" title="" class=" " target="_blank">
            <img src="/assets/frontend/teaser/images/footer_game/img-gr.png" alt="">
        </a>
        <a href="<?= get_setting('youtube_url') ?>" title="" class=" " target="_blank">
            <img src="/assets/frontend/teaser/images/footer_game/img-yt.png" alt="">
        </a>
    </div>
    <div class="max_rank">
        <div class="footer-ace-inner" itemscope="" itemtype="http://schema.org/Organization">
            <a href="#" class="faq-tink" target="_blank"><span itemprop="legalName"><?= get_setting('company_name', 'VMGE') ?></span></a>
            <p class="footer-link-privacy">
                <a href="support.php" title="Hỗ Trợ" class="bs" target="_blank">Hỗ Trợ</a>
                |
                <a href="download.php" target="_blank" class="bs">Cài Đặt</a>
                |
                <a href="policy.php" title="Điều Khoản" class="bs" target="_blank">Điều Khoản</a>
            </p>
            <p class="tCenter footer-text">Website Phát Triển By <?= get_setting('developed_by', 'TuaansNe') ?></p>
            <!-- <p class="tCenter footer-text">CÔNG TY CỔ PHẦN ACEGAME</p> -->
            <!-- <p class="tCenter footer-text">NGƯỜI CHỊU TRÁCH NHIỆM NỘI DUNG: LÊ VĂN HIẾU</p> -->
            <!-- <p class="tCenter footer-text">Tầng 14, Tòa nhà HM Town, 412 Nguyễn Thị Minh Khai, Phường 5, Quận 3, Thành phố Hồ Chí Minh, Việt Nam</p> -->
            <p class="tCenter footer-text">HOTLINE: <?= get_setting('hotline', '0866468126') ?></p>
            <p class="tCenter footer-text">EMAIL: <?= get_setting('email', 'julyasiin@gmail.com') ?></p>
            <!-- <p class="tCenter footer-text">THỜI GIAN: 8:00 - 22:00 CÁC NGÀY (GMT+7)</p> -->
            <!-- <p class="tCenter footer-text">GIẤY PHÉP NỘI DUNG SỐ: 506/QĐ-BTTTT DO BỘ THÔNG TIN VÀ TRUYỀN THÔNG CẤP NGÀY 31/03/2023</p> -->
            <img src="<?= strpos(get_setting('age_rating_image', '/assets/frontend/home/v1/images/18_new.png'), '/') === 0 ? get_setting('age_rating_image', '/assets/frontend/home/v1/images/18_new.png') : '/' . get_setting('age_rating_image', '/assets/frontend/home/v1/images/18_new.png') ?>" width="255" height="100" class="footer-ace-18">
        </div>
    </div>
</div>

<div class="sidebar_right hidden__mobile" style="top: 35%;">
    <div class="sidebar_right-content tCenter">
        <img src="/assets/frontend/home/v1/images/sibarRight/qr.png" alt="" class="icon-right" />
        
        <div class="tCenter t-lineok">
            <img src="/assets/frontend/home/v1/images/sibarRight/line.png" alt="" class="line" />
        </div>
        
        <a target="_blank" href="<?= get_setting('ios_download_url') ?>" class="link-dlgame img-hv p-r">
            <img src="/assets/frontend/home/v1/images/sibarRight/ios.png" alt="" class="img-bt" />
            <img src="/assets/frontend/home/v1/images/sibarRight/ios-hv.png" alt="" class="img-hv p-a in-img-hv" />
        </a>
        
        <a target="_blank" href="<?= get_setting('android_download_url') ?>" class="link-dlgame linkdks-android img-hv p-r">
            <img src="/assets/frontend/home/v1/images/sibarRight/android.png" alt="" class="img-bt" />
            <img src="/assets/frontend/home/v1/images/sibarRight/android-hv.png" alt="" class="img-hv p-a in-img-hv" />
        </a>
        
        <div class="clickGet m__inline">
            <a target="_blank" href="<?= get_setting('payment_url') ?>" class="a100 f-tahomabold tCenter tUpper dFlex aCenter jCenter">
                Nạp thẻ
            </a>
        </div>
        
        <div class="go-top">
            <img src="/assets/frontend/home/v1/images/sibarRight/top.png" alt="" />
        </div>
    </div>
    <span class="ctFixRight dFlex aCenter jCenter">
        <img src="/assets/frontend/home/v1/images/sibarRight/img-arrow.png" class="imgCtr" />
    </span>
</div>

<style>
.footer-ace {
    width: 100%;
    padding: 40px 0 30px;
    text-align: center;
    color: #fff;
    background: #181818;
    font-family: Tahoma, Arial, Helvetica, sans-serif;
    font-size: 14px;
    line-height: 1.5;
}

.footer-link-privacy {
    margin-bottom: 10px;
}

.footer-link-privacy a {
    color: #fff;
    text-decoration: none;
}

.footer-link-privacy a:hover {
    color: #ffa000;
}

.footer-ace p {
    margin-bottom: 6px;
}

.footer-ace-inner {
    width: 100%;
    max-width: 1000px;
    color: #fff;
    font-size: 13px;
    text-align: center;
    position: relative;
    margin: 0 auto;
}

.faq-tink {
    position: absolute;
    display: block;
    text-indent: -999em;
    background: url(/assets/frontend/home/v1/images/logo_footer.png) 0 0 no-repeat;
    background-size: contain;
    width: 110px;
    height: 55px;
    left: 0;
    top: -10px;
}

.footer-ace-18 {
    position: absolute;
    right: 0;
    top: 0;
    max-width: 160px;
    object-fit: contain;
    object-position: top center;
}

@media (max-width: 768px) {
    .faq-tink {
        position: inherit;
        top: 0;
        margin: 0 auto 10px;
    }
    
    .footer-ace-18 {
        display: block;
        position: relative;
        left: 50%;
        margin-top: 10px;
        transform: translateX(-50%);
        margin-left: 0;
    }
}

/* Fix cho sidebar */
.sidebar_right {
    transition: right 0.5s ease;
    /* Đảm bảo sidebar mặc định là ẩn */
    right: -206px !important;
}

.sidebar_right .sidebar_right-content {
    opacity: 0;
    transition: opacity 0.3s ease 0.2s; /* Delay để animation mượt hơn */
}

/* Khi sidebar mở */
.sidebar_right.mo {
    right: 6px !important;
}

.sidebar_right.mo .sidebar_right-content {
    opacity: 1;
}

/* Đảm bảo sidebar không bị che bởi z-index thấp */
.sidebar_right {
    z-index: 999 !important;
}

.sidebar_right.mo {
    z-index: 9999 !important;
}

/* Fix cho nút toggle */
.ctFixRight {
    cursor: pointer;
    transition: all 0.3s ease;
}

.ctFixRight .imgCtr {
    transition: transform 0.3s ease;
}

/* Khi sidebar đóng, mũi tên hướng trái (mặc định) */
.ctFixRight .imgCtr {
    transform: rotateZ(-180deg);
}

/* Khi sidebar mở, mũi tên hướng phải */
.ctFixRight.ctFixRight-mo .imgCtr {
    transform: rotateZ(0deg);
}

/* Fix cho mobile */
@media (max-width: 768px) {
    .sidebar_right {
        display: none !important;
    }
}
</style>
