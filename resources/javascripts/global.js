// Definindo a variável fora do $(document).ready()
let g_CapsLockIsEnabled = null;

$(document).ready(function() {

    if (typeof document.msCapsLockWarningOff !== 'undefined') {
        document.msCapsLockWarningOff = true;
    }

    $('input[type=password]').on({
        'keyup': function(e) {
            CheckForCapsLock(e);
            //TogglePasswordFieldColor();
            //TogglePasswordBackground(e);
            //TogglePasswordLabelColor();
            ShowOrHideCapsLockWarning();
        },
        'focus': function(e) {
            CheckForCapsLock(e);
            //TogglePasswordFieldColor();
            //TogglePasswordLabelColor();
            //TogglePasswordBackground(e);
            //TogglePasswordLabelColor();
            ShowOrHideCapsLockWarning();
        },
        'blur': function() {
            //TogglePasswordLabelColor();
        },
        'click': function() {
            //TogglePasswordFieldColor();
        }
    });

    $(document).on({
        'keydown keypress': function(e) {
            CheckForCapsLock(e);
            //TogglePasswordFieldColor();
            //TogglePasswordLabelColor();
            ShowOrHideCapsLockWarning();
        },
        'keydown': function(e) {
            CheckForCapsLock(e);
        },
        'keypress': function(e) {
            CheckForCapsLock(e);
        },
        'click': function(e) {
            CheckForCapsLock(e);
            //TogglePasswordFieldColor();
            //TogglePasswordLabelColor();
            ShowOrHideCapsLockWarning();
        }
    });
});

function CheckForCapsLock(e) {
    if (!e) {
        console.error("Error: Event object is required");
        return;
    }

    let chr = GetChar(e);
    if (!chr) return;

    if (chr.toLowerCase() == chr.toUpperCase()) return;

    let capsLockOn = e.getModifierState && e.getModifierState('CapsLock');
    g_CapsLockIsEnabled = (chr.toLowerCase() == chr && capsLockOn) || (chr.toUpperCase() == chr && !capsLockOn);
}

function GetChar(e) {
    if (e.which == null) {
        return String.fromCharCode(e.keyCode);
    }

    if (e.which != 0 && e.charCode != 0) {
        return String.fromCharCode(e.which);
    }

    return null;
}

function TogglePasswordFieldColor() {
    let passwordInputs = $('input[type=password]');
    if (g_CapsLockIsEnabled) {
        passwordInputs.not(':focus').addClass('CapsLockHighlight');
    } else {
        passwordInputs.removeClass('CapsLockHighlight');
    }
}

function resetPasswordFieldsColor() {
    let passwordInputs = $('input[type=password]');
    passwordInputs.removeClass('CapsLockHighlight');
}

function TogglePasswordLabelColor() {
    if (g_CapsLockIsEnabled) {
        $('.PasswordLabel').addClass('CapsLockText');
        CapsLockWarning
    } else {
        $('.PasswordLabel').removeClass('CapsLockText');
    }
}

function TogglePasswordBackground(e) {
    let passwordInput = $(e.target);
    if (g_CapsLockIsEnabled) {
        passwordInput.addClass('CapsLockEnabled');
    } else {
        passwordInput.removeClass('CapsLockEnabled');
    }
}

function ShowOrHideCapsLockWarning() {
    if (g_CapsLockIsEnabled) {
        let l_Top = ($('input[type=password]').offset().top + 0);
        let l_Left = ($('input[type=password]').offset().left + 0);
        $('#CapsLockWarning').css({ top: l_Top, left: l_Left });
        $('#CapsLockWarning').show();
    } else {
        $('#CapsLockWarning').hide();
    }
}


$(document).ready(function() {
    CheckForMobileAdjustments();
    $(window).resize(function() {
        CheckForMobileAdjustments();
    });
    $('#MobileShortMenuIcon').click(function() {
        $('#MobileMenuIcon').prop('checked', false);
    });
    $('#MobileMenuIcon').click(function() {
        $('#MobileShortMenuIcon').prop('checked', false);
    });
    $('#MobileShortMenuIcon, #MobileMenuIcon').change(function() {
        CheckForMenuPosition();
    });
});

function CheckForMenuPosition() {
    if ($('#MobileMenuIcon').is(':checked')) {
        $('#MobileMenu').css('height', '100%');
        $('#MobileMenu').css('overflow-y', 'scroll');
    } else {
        $('#MobileMenu').css('height', '70px');
        $('#MobileMenu').css('overflow-y', 'unset');
    }
}

function CheckForMobileAdjustments() {
    if (window.matchMedia('(max-width: 768px)').matches) {
        if ($('#webshop').length > 0 && $('#HelperDivContainer').length > 0) {
            $('#HelperDivContainer').remove();
        }
        var AvailableScreenWidth = window.screen.availWidth;
        if (window.matchMedia("(orientation: landscape)").matches) {
            if (window.screen.availHeight > AvailableScreenWidth) {
                AvailableScreenWidth = window.screen.availHeight;
            }
        }
        var ImageSizeBuffer = 20;
        var TableSizeBuffer = 30;
        var TableEventCalenderBuffer = 40;
        var ObserverInputBuffer = 70;
        var ObserverInnerTableBuffer = ReportTableBuffer = 50;
        var ReportTableBufferForum = 57;
        var ForumImageBuffer = 62;
        var AuctionFilterTableBuffer = 67;
        $('.BoxContent img').each(function() {
            $(this).css('max-width', ((AvailableScreenWidth) - ImageSizeBuffer));
        });
        $('.TableContainer .InnerTableContainer').each(function() {
            $(this).css('max-width', ((AvailableScreenWidth) - TableSizeBuffer));
        });
        $('.TableContainer .TableScrollbarWrapper').each(function() {
            $(this).css('width', ((AvailableScreenWidth) - TableSizeBuffer));
        });
        $(' #EventSchedule .TableContainer .InnerTableContainer').each(function() {
            $(this).css('max-width', ((AvailableScreenWidth) - TableEventCalenderBuffer));
        });
        $('.ThreadReviewPosting img, .PostText img').each(function() {
            $(this).css('max-width', ((AvailableScreenWidth) - ForumImageBuffer));
        });
        $('#ConnectTibiaObserver .TableContentContainer div').each(function() {
            $(this).css('max-width', ((AvailableScreenWidth) - ObserverInnerTableBuffer));
        });
        $('#ConnectTibiaObserver #TibiaObserverTokenInput').each(function() {
            $(this).css('max-width', ((AvailableScreenWidth) - ObserverInputBuffer));
        });
        $('#reason_description, #translation_input, #comment_input, #reasonid_select').each(function() {
            $(this).css('width', ((AvailableScreenWidth) - ReportTableBuffer));
        });
        $('.ForumReport').each(function() {
            $(this).css('width', ((AvailableScreenWidth) - ReportTableBufferForum));
        });
        $('.AuctionInputSearch, .AuctionFilterCategory').each(function() {
            $(this).css('width', ((AvailableScreenWidth) - AuctionFilterTableBuffer));
            if ($(this).hasClass('AuctionFilterCategory')) {
                $(this).css('width', parseInt($(this).css('width')) + 8);
            }
        });
        $('.InInputResetButton').each(function() {
            $(this).css('left', parseInt($('.AuctionInputSearch').css('width')) - parseInt($(this).css('width')) * 0.75);
        });
        $('.TableContainer').each(function() {
            var TableScrollbarWrapper = $(this).find('.TableScrollbarWrapper');
            var InnerTableContainer = $(this).find('.InnerTableContainer');
            var ScrollWidth = $(this).find('.InnerTableContainer')[0].scrollWidth;
            if (ScrollWidth > ((AvailableScreenWidth) - TableSizeBuffer)) {
                $(this).find('.TableScrollbarContainer').width(ScrollWidth);
                InnerTableContainer.css('margin-bottom', 5);
                TableScrollbarWrapper.css('display', 'block');
                TableScrollbarWrapper.scroll(function(Event) {
                    InnerTableContainer.scrollLeft(Event.target.scrollLeft);
                });
                InnerTableContainer.scroll(function(Event) {
                    TableScrollbarWrapper.scrollLeft(Event.target.scrollLeft);
                });
            }
        });
        var NewsImageBuffer = 20;
        var NewsTableBuffer = 20;
        $('.Content .NewsTable img').each(function() {
            $(this).removeAttr('width');
            $(this).removeAttr('height');
            $(this).removeAttr('vspace');
            $(this).removeAttr('hspace');
        });
        $('.Content .NewsTable img').each(function() {
            $(this).css('max-width', ((AvailableScreenWidth) - NewsImageBuffer));
        });
        $('.Content .NewsTable figure').each(function() {
            $(this).css('margin-left', 0);
        });
        $('.Content .NewsTable figure').each(function() {
            $(this).css('margin-right', 0);
        });
        $('.Content .NewsTable .NewsTableContainer table').each(function() {
            $(this).css('display', 'block');
        });
        $('.Content .NewsTable .NewsTableContainer table').each(function() {
            $(this).css('width', 'unset');
        });
        $('.Content .NewsTable .NewsTableContainer table').each(function() {
            $(this).css('height', 'unset');
        });
        $('.Content .NewsTable .NewsTableContainer table').each(function() {
            $(this).css('overflow-x', 'scroll');
        });
        $('.Content .NewsTable .NewsTableContainer table').each(function() {
            $(this).css('max-width', (AvailableScreenWidth) - NewsTableBuffer);
        });
    } else {
        $('.TableContainer .InnerTableContainer').each(function() {
            $(this).css('max-width', 'unset');
        });
        $('.TableContainer .TableScrollbarWrapper').each(function() {
            $(this).css('width', 'unset');
        });
    }
    return;
}
$(document).ready(function() {
    let g_PasswordToolTipIsActive = 0;
    if ($('.PWStrengthContainer').length > 0) { g_PasswordToolTipIsActive = 1; }
    if ($('#password1').length > 0) {
        $("#password1").change(function() { CheckPasswordStrength($('#password1').val()); });
        $("#password1").keyup(function() { CheckPasswordStrength($('#password1').val()); });
        if ($('#password1').val().length > 0) { CheckPasswordStrength($('#password1').val()); }
        $(".Themeboxes").css({ zIndex: 10 })
    }
});

function CheckPasswordStrength(a_Password) {
    if (a_Password.length > 0) {
        $('.PWStrengthToolTip').show();
        $('#password_errormessage .CanBeHiddenWhenToolTipIsOn').hide();
        $('.BoxInputText #password_errormessage').hide();
    }
    let l_Feedback = '';
    let l_CSSClass = 'PWStrengthIndicator';
    let l_Warning = '';
    let l_Suggestions = '';
    let l_Result = { 'score': 0, 'feedback': { 'warning': '', 'suggestions': Array(0) } };
    let l_RulesFullfilled = ValidatePassword(a_Password);
    if (l_RulesFullfilled) {
        l_Result = zxcvbn(a_Password);
    }
    if (l_Result.score > 3) {
        l_Feedback = 'strong';
        l_CSSClass += ' PWStrengthLevel4';
    } else if (l_Result.score == 3) {
        l_Feedback = 'medium';
        l_CSSClass += ' PWStrengthLevel3';
    } else if (l_Result.score == 2) {
        l_Feedback = 'weak';
        l_CSSClass += ' PWStrengthLevel2';
    } else if (l_Result.score == 1) {
        l_Feedback = 'very weak';
        l_CSSClass += ' PWStrengthLevel1';
    } else {
        l_Feedback = 'very weak';
        l_CSSClass += ' PWStrengthLevel0';
    }
    $('.PWStrengthIndicator').text(l_Feedback);
    $('.PWStrengthIndicator').attr('class', l_CSSClass);
    $('.PWStrengthSuggestions').text(l_Suggestions);
    $('.PWStrengthWarning').text(l_Warning);
    if (l_Result.score <= 2) {
        if (l_Result.feedback.warning.length > 0) {
            l_Warning += l_Result.feedback.warning;
            $('.PWStrengthWarning').append('<div class="PWStrengthToolTipHeadline" >Warning</div>');
            $('.PWStrengthWarning').append('<div>' + l_Warning + '</div>');
        }
        if (l_Result.feedback.suggestions.length > 0) {
            $('.PWStrengthSuggestions').append('<div class="PWStrengthToolTipHeadline" >Tip</div>');
            for (let i = 0; i < l_Result.feedback.suggestions.length; i++) {
                $('.PWStrengthSuggestions').append('<div>' + l_Result.feedback.suggestions[i] + '</div>');
            }
        }
    }
    if (l_RulesFullfilled == true) { $('.PWStrengthToolTip').hide(); }
}

function ValidatePassword(a_Password) {
    let l_ReturnValue = true;
    let PWRules = [
        [62, ((a_Password.length >= 30 || a_Password.length < 10) ? false : true)],
        [63, ((a_Password.match(/[^!-~]+/) !== null) ? false : true)],
        [65, ((a_Password.match(/^[A-Za-z]*$/) !== null) ? false : true)],
        [84, ((a_Password.match(/[a-z]/) === null) ? false : true)],
        [85, ((a_Password.match(/[A-Z]/) === null) ? false : true)],
        [86, ((a_Password.match(/[0-9]/) === null) ? false : true)]
    ];
    for (let i = 0; i < PWRules.length; i++) {
        if (PWRules[i][1]) {
            $('#PWRule' + PWRules[i][0]).removeClass('InputIndicatorNotOK');
            $('#PWRule' + PWRules[i][0]).addClass('InputIndicatorOK');
        } else {
            l_ReturnValue = false;
            $('#PWRule' + PWRules[i][0]).addClass('InputIndicatorNotOK');
            $('#PWRule' + PWRules[i][0]).removeClass('InputIndicatorOK');
        }
    }
    return l_ReturnValue;
}

function SetCookie(a_Name, a_Value, a_ExpireDateUTCString) {
    var l_CookieExpireString = '';
    if (a_ExpireDateUTCString != false) {
        l_CookieExpireString += ' expires=' + a_ExpireDateUTCString + ';';
    }
    //document.cookie = a_Name + '=' + encodeURIComponent(a_Value) + '; domain=' + JS_COOKIE_DOMAIN + ';' + l_CookieExpireString + ' path=/;';
    document.cookie = a_Name + "=" + encodeURIComponent(a_Value) + ";" + "expires=" + l_CookieExpireString + ";" + "path=/";
    return;
}

function GetCookieValue(a_Name) {
    var l_RequestedCookieValue = null;
    var l_ConsentCookieEQ = a_Name + '=';
    var l_AllCookies = document.cookie.split(';');
    for (var i = 0; i < l_AllCookies.length; i++) {
        var l_SingleCookie = l_AllCookies[i];
        while (l_SingleCookie.charAt(0) == ' ') {
            l_SingleCookie = l_SingleCookie.substring(1, l_SingleCookie.length);
        }
        if (l_SingleCookie.indexOf(l_ConsentCookieEQ) == 0) {
            l_RequestedCookieValue = decodeURIComponent(l_SingleCookie.substring(l_ConsentCookieEQ.length, l_SingleCookie.length));
            break;
        }
    }
    return l_RequestedCookieValue;
}

function HideCookieDialog() { $('#cookiedialogbox').hide(); }

function ShowCookieDialog() { $('#cookiedialogbox').show(); }

function HideCookieDetails() { $('#cookiedetailsbox').hide(); }

function ShowCookieDetails() { $('#cookiedetailsbox').show(); }

function SetConsentCookie(consent, acceptall) {
    var cc_advertising = false;
    var cc_social = false;
    if (consent == true) {
        if (acceptall == true) {
            cc_advertising = true;
            cc_social = true;
        } else {
            cc_advertising = $("#cc_advertising").is(':checked');
            cc_social = $('#cc_social').is(':checked');
        }
    }
    var settings = {
        consent: consent,
        advertising: cc_advertising,
        socialmedia: cc_social
    };
    SetCookie('CookieConsentPreferences', JSON.stringify(settings), GetConsentCookieExpireDateUTCString());
    location.reload();
}

function ProlongConsentCookie() {
    var html = "<div id='cookiedialogbackground'><div id='cookiedialogbox'><div class='TableContainer'> <div class='CaptionContainer'> <div class='CaptionInnerContainer'> <span class='CaptionEdgeLeftTop' style='background-image:url(https://static.tibia.com/images/global/content/box-frame-edge.gif);'></span> <span class='CaptionEdgeRightTop' style='background-image:url(https://static.tibia.com/images/global/content/box-frame-edge.gif);'></span> <span class='CaptionBorderTop' style='background-image:url(https://static.tibia.com/images/global/content/table-headline-border.gif);'></span> <span class='CaptionVerticalLeft' style='background-image:url(https://static.tibia.com/images/global/content/box-frame-vertical.gif);'></span> <div class='Text'>Stop! What about cookies?</div> <span class='CaptionVerticalRight' style='background-image:url(https://static.tibia.com/images/global/content/box-frame-vertical.gif);'></span> <span class='CaptionBorderBottom' style='background-image:url(https://static.tibia.com/images/global/content/table-headline-border.gif);'></span> <span class='CaptionEdgeLeftBottom' style='background-image:url(https://static.tibia.com/images/global/content/box-frame-edge.gif);'></span> <span class='CaptionEdgeRightBottom' style='background-image:url(https://static.tibia.com/images/global/content/box-frame-edge.gif);'></span> </div> </div><table class='Table5' cellspacing='0' cellpadding='0'>  <tbody><tr> <td> <div class='TableScrollbarWrapper' style='width: unset;'> <div class='TableScrollbarContainer'> </div> </div> <div class='InnerTableContainer' style='max-width: unset;'> <div id='cookiedialogimage'><img src='https://static.tibia.com/images/global/general/warning-druid.png'></div><div id='cookiedialogcontent'>Our website makes use of cookies (sadly not the delicious, crumbly ones) and similar technologies. If you accept them, we share information with our partners for social media, advertising and analysis.<br><br>Please let us know which cookies we can use.<div class='SubmitButtonRow'><div class='LeftButton'><div class='BigButton' style='background-image:url(https://static.tibia.com/images/global/buttons/button_blue.gif)'><div class='ButtonEventHook' onmouseover='MouseOverBigButton(this);' onmouseout='MouseOutBigButton(this);'><div class='BigButtonOver' style='background-image:url(https://static.tibia.com/images/global/buttons/button_blue_over.gif);'></div><span class='BigButtonText' onclick='HideCookieDialog();ShowCookieDetails();'>Manage Cookies</span></div></div></div><div class='RightButton'><div class='BigButton' style='background-image:url(https://static.tibia.com/images/global/buttons/button_green.gif)'><div class='ButtonEventHook' onmouseover='MouseOverBigButton(this);' onmouseout='MouseOutBigButton(this);'><div class='BigButtonOver' style='background-image:url(https://static.tibia.com/images/global/buttons/button_green_over.gif);'></div><span class='BigButtonText' onclick='SetConsentCookie(true, true);'>Accept All</span></div></div></div></div></div><table style='width:100%;'> </table> </div> </td> </tr> </tbody></table></div></div><div id='cookiedetailsbox'><div class='TableContainer'> <div class='CaptionContainer'> <div class='CaptionInnerContainer'> <span class='CaptionEdgeLeftTop' style='background-image:url(https://static.tibia.com/images/global/content/box-frame-edge.gif);'></span> <span class='CaptionEdgeRightTop' style='background-image:url(https://static.tibia.com/images/global/content/box-frame-edge.gif);'></span> <span class='CaptionBorderTop' style='background-image:url(https://static.tibia.com/images/global/content/table-headline-border.gif);'></span> <span class='CaptionVerticalLeft' style='background-image:url(https://static.tibia.com/images/global/content/box-frame-vertical.gif);'></span> <div class='Text'>Manage Cookies</div> <span class='CaptionVerticalRight' style='background-image:url(https://static.tibia.com/images/global/content/box-frame-vertical.gif);'></span> <span class='CaptionBorderBottom' style='background-image:url(https://static.tibia.com/images/global/content/table-headline-border.gif);'></span> <span class='CaptionEdgeLeftBottom' style='background-image:url(https://static.tibia.com/images/global/content/box-frame-edge.gif);'></span> <span class='CaptionEdgeRightBottom' style='background-image:url(https://static.tibia.com/images/global/content/box-frame-edge.gif);'></span> </div> </div><table class='Table5' cellspacing='0' cellpadding='0'>  <tbody><tr> <td> <div class='TableScrollbarWrapper' style='width: unset;'> <div class='TableScrollbarContainer'> </div> </div> <div class='InnerTableContainer' style='max-width: unset;'> <table style='width:100%;'><tbody><tr><td> <div class='TableContentContainer'> <table class='TableContent' style='border:1px solid #faf0d7;' width='100%'><tbody><tr><td><label class='switch'><input type='checkbox' id='cc_necessary' checked='true' disabled='disabled'><span class='slider round'></span></label></td><td class='cookiedetailscontent'><h3>Necessary</h3>These cookies are required in order for our website to function (e.g. logging in). If you set your browser to block or alert you about these cookies, some parts of the website might not work.<br><br></td></tr> </tbody></table> </div></td></tr><tr><td> <div class='TableContentContainer'> <table class='TableContent' style='border:1px solid #faf0d7;' width='100%'><tbody><tr><td><label class='switch'><input type='checkbox' id='cc_advertising'><span class='slider round'></span></label></td><td class='cookiedetailscontent'><h3>Targeting and Advertising</h3>Advertisers and other content providers that may appear on our website may also use cookies that are not sent by us. Such advertisements or content may use cookies to help track and target the interests of users of the website to present customised and personalised advertisements or other messages that the user might find interesting. We also use these cookies and so-called Tracking Pixels of our partners to measure and improve the effectiveness of marketing campaigns. (Facebook Tracking Pixel)<br><br></td></tr> </tbody></table> </div></td></tr><tr><td> <div class='TableContentContainer'> <table class='TableContent' style='border:1px solid #faf0d7;' width='100%'><tbody><tr><td><label class='switch'><input type='checkbox' id='cc_social'><span class='slider round'></span></label></td><td class='cookiedetailscontent'><h3>Social Media</h3>These cookies enable the website to provide enhanced functionality and personalisation. They may be set by third-party providers (like social networks or streaming platforms) whose services we use on the website. If you do not allow these cookies, some or all of these services may not function properly. (Twitter, YouTube, Facebook)<br><br></td></tr> </tbody></table> </div></td></tr><tr><td colspan='2'><div class='SubmitButtonRow'><div class='LeftButton'><div class='BigButton' style='background-image:url(https://static.tibia.com/images/global/buttons/button_blue.gif)'><div class='ButtonEventHook' onmouseover='MouseOverBigButton(this);' onmouseout='MouseOutBigButton(this);'><div class='BigButtonOver' style='background-image:url(https://static.tibia.com/images/global/buttons/button_blue_over.gif);'></div><span class='BigButtonText' onclick='HideCookieDetails();ShowCookieDialog();'>Close</span></div></div></div><div class='CenterButton'><div class='BigButton' style='background-image:url(https://static.tibia.com/images/global/buttons/button_blue.gif)'><div class='ButtonEventHook' onmouseover='MouseOverBigButton(this);' onmouseout='MouseOutBigButton(this);'><div class='BigButtonOver' style='background-image:url(https://static.tibia.com/images/global/buttons/button_blue_over.gif);'></div><span class='BigButtonText' onclick='SetConsentCookie(true, false);'>Save Settings</span></div></div></div><div class='RightButton'><div class='BigButton' style='background-image:url(https://static.tibia.com/images/global/buttons/button_green.gif)'><div class='ButtonEventHook' onmouseover='MouseOverBigButton(this);' onmouseout='MouseOutBigButton(this);'><div class='BigButtonOver' style='background-image:url(https://static.tibia.com/images/global/buttons/button_green_over.gif);'></div><span class='BigButtonText' onclick='SetConsentCookie(true, true);'>Accept All</span></div></div></div></div></td></tr> </tbody></table> </div> </td> </tr> </tbody></table></div></div></div>";

    var l_ConsentCookie = GetCookieValue('CookieConsentPreferences');
    if (l_ConsentCookie != null) {
        SetCookie('CookieConsentPreferences', l_ConsentCookie, GetConsentCookieExpireDateUTCString());
        $('#cookiedialogbox').hide();
    } else {
        document.getElementById('cookiedialogbackground').innerHTML = html;
    }
}

function GetConsentCookieExpireDateUTCString() {
    var l_ExpireDate = new Date;
    l_ExpireDate.setFullYear(l_ExpireDate.getFullYear() + 1);
    return l_ExpireDate.toUTCString();
}

$(document).ready(function() {
    var l_CurrentDomain = window.location.host.toString();
    var l_CookieDomain = JS_COOKIE_DOMAIN;
    if ((l_CookieDomain.includes(l_CurrentDomain) == true) || (l_CurrentDomain.includes(l_CookieDomain) == true)) {
        ProlongConsentCookie();
    }
});

function FansiteFilterAction(a_Element) {
    if (a_Element == 'Language_All' || a_Element == 'SocialMedia_All' || a_Element == 'Content_All') {
        if ($('#' + a_Element).hasClass('FilterIsActive')) {
            return;
        }
    }
    if (a_Element == 'Language_All') {
        $('.FilterElementLanguage').addClass('FilterIsActive');
    } else if (a_Element == 'SocialMedia_All') {
        $('.FilterElementSocialMedia').addClass('FilterIsActive');
    } else if (a_Element == 'Content_All') {
        $('.FilterElementContent').addClass('FilterIsActive');
    }
    if ($('#' + a_Element).hasClass('FilterIsActive')) {
        $('#' + a_Element).removeClass('FilterIsActive');
    } else {
        $('#' + a_Element).addClass('FilterIsActive');
    }
    if ($('#' + a_Element).hasClass('FilterElementLanguage')) {
        $('.FilterLanguageAll').removeClass('FilterIsActive');
    } else if ($('#' + a_Element).hasClass('FilterElementSocialMedia')) {
        $('.FilterSocialMediaAll').removeClass('FilterIsActive');
    } else if ($('#' + a_Element).hasClass('FilterElementContent')) {
        $('.FilterContentAll').removeClass('FilterIsActive');
    }
    if ($('.FilterElementLanguage').hasClass('FilterIsActive') == false) {
        $('#Language_All').addClass('FilterIsActive');
    }
    if ($('.FilterElementSocialMedia').hasClass('FilterIsActive') == false) {
        $('#SocialMedia_All').addClass('FilterIsActive');
    }
    if ($('.FilterElementContent').hasClass('FilterIsActive') == false) {
        $('#Content_All').addClass('FilterIsActive');
    }
    var l_Filter = '';
    l_Filter = '.FilterElementLanguage.FilterIsActive';
    if ($('#Language_All').hasClass('FilterIsActive')) {
        l_Filter = '.FilterElementLanguage';
    }
    var l_ActiveLanguageList = $(l_Filter).not('.FilterAll').map(function() {
        return '.Filter' + $(this).attr('id');
    }).get().join(', ');
    var l_LanguageRows = $(l_ActiveLanguageList).map(function() {
        return '#' + $(this).attr('id');
    }).get();
    l_Filter = '.FilterElementSocialMedia.FilterIsActive';
    if ($('#SocialMedia_All').hasClass('FilterIsActive')) {
        l_Filter = '.FilterElementLanguage';
    }
    var l_ActiveSocialMediaList = $(l_Filter).not('.FilterAll').map(function() {
        return '.Filter' + $(this).attr('id');
    }).get().join(', ');
    var l_SocialMediaRows = $(l_ActiveSocialMediaList).map(function() {
        return '#' + $(this).attr('id');
    }).get();
    l_Filter = '.FilterElementContent.FilterIsActive';
    if ($('#Content_All').hasClass('FilterIsActive')) {
        l_Filter = '.FilterElementLanguage';
    }
    var l_ActiveContentList = $(l_Filter).not('.FilterAll').map(function() {
        return '.Filter' + $(this).attr('id');
    }).get().join(', ');
    var l_ContentRows = $(l_ActiveContentList).map(function() {
        return '#' + $(this).attr('id');
    }).get();
    var l_FilteredListRowIDs = $(l_LanguageRows).filter(l_SocialMediaRows).filter(l_ContentRows).get().join(', ');
    $('.FilterResultRow').hide();
    $(l_FilteredListRowIDs).show();
    if (a_Element == 'Language_All') {
        $('.FilterElementLanguage').removeClass('FilterIsActive');
    } else if (a_Element == 'SocialMedia_All') {
        $('.FilterElementSocialMedia').removeClass('FilterIsActive');
    } else if (a_Element == 'Content_All') {
        $('.FilterElementContent').removeClass('FilterIsActive');
    }
    return;
}

function MouseOverBigButton(source) {
    source.firstChild.style.visibility = "visible";
}

function MouseOutBigButton(source) {
    source.firstChild.style.visibility = "hidden";
}

function CopyContentOfFormInput(a_SourceID, a_FormInputID) {
    $("#" + a_SourceID).click(function() {
        $("#" + a_FormInputID).select();
        document.execCommand("copy");
    });
    $("#" + a_FormInputID).click(function() { $(this).select(); });
}