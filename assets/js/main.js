$(document).ready(function() {
    // Sidebar right toggle functionality
    function initSidebar() {
        // Kiểm tra trạng thái sidebar từ localStorage
        var sidebarState = localStorage.getItem('sidebarState');
        
        // Nếu không có state hoặc state là 'closed', đảm bảo sidebar đóng
        if (!sidebarState || sidebarState === 'closed') {
            $('.sidebar_right').removeClass('mo');
            $('.ctFixRight').removeClass('ctFixRight-mo');
        } else if (sidebarState === 'open') {
            $('.sidebar_right').addClass('mo');
            $('.ctFixRight').addClass('ctFixRight-mo');
        }
        
        // Toggle sidebar when clicked
        $('.ctFixRight').click(function() {
            $('.sidebar_right').toggleClass('mo');
            $(this).toggleClass('ctFixRight-mo');
            
            // Lưu trạng thái vào localStorage
            if ($('.sidebar_right').hasClass('mo')) {
                localStorage.setItem('sidebarState', 'open');
            } else {
                localStorage.setItem('sidebarState', 'closed');
            }
        });
    }
    
    // Initialize sidebar
    initSidebar();

    // Go to top
    var offset = 1080;
    var go_top = $(".go-top");
    
    go_top.click(function() {
        $("html,body").animate({ scrollTop: 0 }, 500);
    });
    
    $(window).scroll(function() {
        if ($(window).scrollTop() > offset) {
            go_top.fadeIn();
        } else {
            go_top.fadeOut();
        }
    });

    // AOS Animation
    if (typeof AOS !== 'undefined') {
        AOS.init({
            once: true,
        });
    }

    // Slick slider for news slides
    if ($(".listSlide__new").length) {
        $(".listSlide__new").slick({
            dots: true,
            prevArrow: false,
            nextArrow: false,
            autoplay: true,
            speed: 500,
            adaptiveHeight: true
        });
    }

    // Features slider
    if ($(".slide__feature").length) {
        $(".slide__feature").slick({
            infinite: true,
            autoplay: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
            dots: true,
        });
    }

    // Tab functionality for news
    $(".tab-new .tab-link").click(function() {
        var tab_id = $(this).attr("data-tab");
        var tab_view = $(this).attr("data-more");

        $(".tab-new .tab-link").removeClass("current");
        $(".tab-detail").removeClass("current");
        $(".link-more").removeClass("current");

        $(this).addClass("current");
        $("#" + tab_id).addClass("current");
        $("#" + tab_view).addClass("current");
    });

    // Video handling
    function handlePlay() {
        let _width = $(window).width();
        if (_width >= 1200) {
            var video = $("#videoBgPC").get(0);
            if (video) {
                // Đảm bảo video có thể phát
                video.muted = true;
                video.playsInline = true;
                video.play().catch(function(error) {
                    console.log("Video autoplay failed:", error);
                });
            }
            $(".textgame__game").css("opacity", 0);
        } else {
            $(".textgame__game").css("opacity", 1);
        }
    }

    $(window).on("resize", function() {
        handlePlay();
    });

    $(window).on("scroll load", function() {
        handlePlay();
    });
    
    // Thêm sự kiện để video tự phát khi page load
    $(window).on("load", function() {
        setTimeout(handlePlay, 500);
    });

    // Logo animation
    const animationLogo = document.querySelector(".text--brand");
    if (animationLogo) {
        animationLogo.classList.add("active");
        setInterval(() => {
            animationLogo.classList.remove("active");
            setTimeout(() => {
                animationLogo.classList.add("active");
            }, 200);
        }, 7000);
    }

    // Form validation
    $('form').submit(function(e) {
        var form = $(this);
        var hasError = false;

        // Check required fields
        form.find('input[required]').each(function() {
            if (!$(this).val().trim()) {
                hasError = true;
                $(this).css('border-color', '#e53e3e');
            } else {
                $(this).css('border-color', '#ddd');
            }
        });

        // Check password match
        var password = form.find('input[name="password"]').val();
        var confirmPassword = form.find('input[name="confirm_password"]').val();
        
        if (password && confirmPassword && password !== confirmPassword) {
            hasError = true;
            form.find('input[name="confirm_password"]').css('border-color', '#e53e3e');
            alert('Mật khẩu xác nhận không khớp!');
        }

        if (hasError) {
            e.preventDefault();
        }
    });

    // Clear error styling on input focus
    $('input').focus(function() {
        $(this).css('border-color', '#667eea');
    });

    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);

    // Smooth scrolling for anchor links
    $('a[href*="#"]').click(function(e) {
        var target = $(this.hash);
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 800);
        }
    });

    // Image lazy loading
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Mobile menu toggle
    $('#toggle-menu__header-page').change(function() {
        if ($(this).is(':checked')) {
            $('body').addClass('menu-open');
        } else {
            $('body').removeClass('menu-open');
        }
    });

    // Mobile user menu toggle
    $('.user-menu > a').click(function(e) {
        if ($(window).width() <= 768) {
            e.preventDefault();
            $(this).parent('.user-menu').toggleClass('active');
        }
    });
    
    // Close user menu when clicking outside on mobile
    $(document).click(function(e) {
        if ($(window).width() <= 768) {
            if (!$(e.target).closest('.user-menu').length) {
                $('.user-menu').removeClass('active');
            }
        }
    });
    
    // Handle window resize
    $(window).resize(function() {
        if ($(window).width() > 768) {
            $('.user-menu').removeClass('active');
        }
    });
});

// Helper functions
function showNotification(message, type = 'info') {
    const notification = $('<div class="notification notification-' + type + '">' + message + '</div>');
    $('body').append(notification);
    
    setTimeout(() => {
        notification.addClass('show');
    }, 100);
    
    setTimeout(() => {
        notification.removeClass('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showNotification('Đã sao chép vào clipboard!', 'success');
    }, function(err) {
        showNotification('Không thể sao chép!', 'error');
    });
}
