function InitializePage() {
    LoadLoginBox();
    LoadMenu();
}

function ActivateWebsiteFrame() {
    g_Deactivated = false;
    if (document.getElementById('DeactivationContainer') != null) {
        document.getElementById('DeactivationContainer').style.display = "none";
    }
    if (document.getElementById('DeactivationContainerThemebox') != null) {
        document.getElementById('DeactivationContainerThemebox').style.display = "none";
    }
}

function DeactivateWebsiteFrame() {
    if (document.getElementById('DeactivationContainer') != null) {
        document.getElementById('DeactivationContainer').style.display = "block";
    }
    if (document.getElementById('DeactivationContainerThemebox') != null) {
        document.getElementById('DeactivationContainerThemebox').style.display = "block";
    }
}

function MouseOverWebshopButton(source) {
    source.firstChild.style.visibility = "visible";
}

function MouseOutWebshopButton(source) {
    source.firstChild.style.visibility = "hidden";
}

function MouseOverMediumButton(source) {
    source.firstChild.style.visibility = "visible";
}

function MouseOutMediumButton(source) {
    source.firstChild.style.visibility = "hidden";
}

function CheckAll(form_name, checkbox_name) {
    var form = document.getElementById(form_name);
    if (form.ALL) {
        var c = form.ALL.checked;
    }
    for (var i = 0; i < form.elements.length; i++) {
        var e = form.elements[i];
        if (e.name != checkbox_name)
            continue;
        e.checked = c;
    }
}

function LoadLoginBox() {
    if (loginStatus == false) {
        document.getElementById('MediumButtonText').style.backgroundImage = "url('" + JS_DIR_IMAGES + "global/buttons/mediumbutton_login.png')";
        document.getElementById('LoginstatusText_1').style.backgroundImage = "url('" + JS_DIR_IMAGES + "global/loginbox/loginbox-font-create-account.gif')";
        document.getElementById('LoginstatusText_2').style.backgroundImage = "url('" + JS_DIR_IMAGES + "global/loginbox/loginbox-font-create-account-over.gif')";
    } else {
        document.getElementById('MediumButtonText').style.backgroundImage = "url('" + JS_DIR_IMAGES + "global/buttons/mediumbutton_myaccount.png')";
        document.getElementById('LoginstatusText_1').style.backgroundImage = "url('" + JS_DIR_IMAGES + "global/loginbox/loginbox-font-logout.gif')";
        document.getElementById('LoginstatusText_2').style.backgroundImage = "url('" + JS_DIR_IMAGES + "global/loginbox/loginbox-font-logout-over.gif')";
    }
}

function MouseOverLoginBoxText(source) {
    source.lastChild.style.visibility = "visible";
    source.firstChild.style.visibility = "hidden";
}

function MouseOutLoginBoxText(source) {
    source.firstChild.style.visibility = "visible";
    source.lastChild.style.visibility = "hidden";
}

function LoginButtonAction() {
    if (loginStatus == "false") {
        window.location = JS_DIR_ACCOUNT + "/account";
    } else {
        window.location = JS_DIR_ACCOUNT + "/account";
    }
}

function LoginstatusTextAction(source) {
    if (loginStatus == "false") {
        window.location = JS_DIR_ACCOUNT + "/createaccount";
    } else {
        window.location = JS_DIR_ACCOUNT + "/logout";
    }
}

var menu = new Array();
menu[0] = new Object();
var unloadhelper = false;
var menuItemName = '';

function LoadMenu() {
    let element = document.getElementById("submenu_" + activeSubmenuItem);
    if (element) {
        element.style.color = "white";
    }
    if (self.name.lastIndexOf("&") == -1) {
        self.name = "news=1&library=0&community=0&account=0&wars=0&support=0&shop=0&";
    }
    FillMenuArray();
    if (window.matchMedia('(max-width: 768px)').matches) {
        var submenuItem = document.getElementById(activeSubmenuItem);
        var ParentID = submenuItem.closest('[class="Level1Block"]').id;
        for (menuItemName in menu[0]) {
            if (menuItemName == ParentID) {
                menu[0][menuItemName] = 1;
                $('.MobileMenuItems #' + ParentID + ' .Level1Entry').addClass('Level1ItemOpen');
            } else {
                menu[0][menuItemName] = 0;
            }
        }
    }
    InitializeMenu();
    $('#MobileMenu .Level2Block').removeClass('MobileMenuActiveItem');
    $('#MobileMenu #' + activeSubmenuItem).addClass('MobileMenuActiveItem');
}

function SaveMenu() {
    if (unloadhelper == false) {
        SaveMenuArray();
        unloadhelper = true;
    }
}

function FillMenuArray() {
    var MenuCount = 0;
    var mark1 = 0;
    var mark2 = 0;
    while (self.name.length > 0) {
        MenuCount++;
        mark1 = self.name.indexOf("=");
        mark2 = self.name.indexOf("&");
        if (MenuCount > 15 || mark1 < 0 || mark2 < 0) {
            break;
        }
        menuItemName = self.name.substr(0, mark1);
        menu[0][menuItemName] = self.name.substring(mark1 + 1, mark2);
        self.name = self.name.substr(mark2 + 1, self.name.length);
    }
}

function InitializeMenu() {
    for (menuItemName in menu[0]) {
        if (menu[0][menuItemName] == "0") {
            document.getElementById(menuItemName + "_Submenu").style.visibility = "hidden";
            document.getElementById(menuItemName + "_Submenu").style.display = "none";
            document.getElementById(menuItemName + "_Lights").style.visibility = "visible";
            document.getElementById(menuItemName + "_Extend").style.backgroundImage = "url(" + JS_DIR_IMAGES + "global/general/plus.gif)";
            $('#MobileMenu #' + menuItemName + ' .Level2Block').hide();
        } else {
            document.getElementById(menuItemName + "_Submenu").style.visibility = "visible";
            document.getElementById(menuItemName + "_Submenu").style.display = "block";
            document.getElementById(menuItemName + "_Lights").style.visibility = "hidden";
            document.getElementById(menuItemName + "_Extend").style.backgroundImage = "url(" + JS_DIR_IMAGES + "global/general/minus.gif)";
            $('#MobileMenu #' + menuItemName + ' .Level2Block').show();
        }
    }
}

function SaveMenuArray() {
    var stringSlices = "";
    var temp = "";
    for (menuItemName in menu[0]) {
        stringSlices = menuItemName + "=" + menu[0][menuItemName] + "&";
        temp = temp + stringSlices;
    }
    self.name = temp;
}

function MenuItemAction(sourceId) {
    if (menu[0][sourceId] == 1) {
        CloseMenuItem(sourceId);
        $('.MobileMenuItems #' + sourceId + ' .Level1Entry').removeClass('Level1ItemOpen');
    } else {
        OpenMenuItem(sourceId);
        $('.MobileMenuItems #' + sourceId + ' .Level1Entry').addClass('Level1ItemOpen');
    }
}

function OpenMenuItem(sourceId) {
    if (window.matchMedia('(max-width: 768px)').matches) {
        for (menuItemName in menu[0]) {
            CloseMenuItem(menuItemName);
        }
    }
    menu[0][sourceId] = 1;
    document.getElementById(sourceId + "_Submenu").style.visibility = "visible";
    document.getElementById(sourceId + "_Submenu").style.display = "block";
    document.getElementById(sourceId + "_Lights").style.visibility = "hidden";
    document.getElementById(sourceId + "_Extend").style.backgroundImage = "url(" + JS_DIR_IMAGES + "global/general/minus.gif)";
    $('#MobileMenu #' + sourceId + ' .Level2Block').show();
}

function CloseMenuItem(sourceId) {
    menu[0][sourceId] = 0;
    document.getElementById(sourceId + "_Submenu").style.visibility = "hidden";
    document.getElementById(sourceId + "_Submenu").style.display = "none";
    document.getElementById(sourceId + "_Lights").style.visibility = "visible";
    document.getElementById(sourceId + "_Extend").style.backgroundImage = "url(" + JS_DIR_IMAGES + "global/general/plus.gif)";
    $('#MobileMenu #' + sourceId + ' .Level2Block').hide();
}

function MouseOverMenuItem(source) {
    source.firstChild.style.visibility = "visible";
}

function MouseOutMenuItem(source) {
    source.firstChild.style.visibility = "hidden";
}

function MouseOverSubmenuItem(source) {
    source.style.backgroundColor = "#14433F";
}

function MouseOutSubmenuItem(source) {
    source.style.backgroundColor = "#0D2E2B";
}

function PaymentStandBy(a_Source, a_Case) {
    let m_Agree = false;
    if (a_Source == "setup" && a_Case != 1) {
        if (document.getElementById("CheckBoxAgreePayment").checked == true) {
            m_Agree = true;
        }
    }
    if (a_Source == "setup" && a_Case == 1) {
        if (document.getElementById("CheckBoxAgreePayment").checked == true && document.getElementById("CheckBoxAgreeSubscription").checked == true) {
            m_Agree = true;
        }
    }
    if (a_Source == "cancel") {
        m_Agree = true;
    }
    if (m_Agree == true) {
        document.getElementById("Step4MinorErrorBox").style.visibility = "hidden";
        document.getElementById("Step4MinorErrorBox").style.display = "none";
        document.getElementById("DisplayText").style.visibility = "hidden";
        document.getElementById("DisplayText").style.display = "none";
        document.getElementById("StandByMessage").style.visibility = "visible";
        document.getElementById("StandByMessage").style.display = "block";
        document.getElementById("DisplaySubmitButton").style.visibility = "hidden";
        document.getElementById("DisplaySubmitButton").style.display = "none";
        document.getElementById("DisplayBackButton").style.visibility = "hidden";
        document.getElementById("DisplayBackButton").style.display = "none";
    }
}

function NoteDownload(a_ClientType) {
    parent.confirmclient.location = JS_DIR_ACCOUNT + 'downloadaction.php?clienttype=' + a_ClientType;
}

function SetFormFocus() {
    if (g_FormName && g_FieldName) {
        let form = document.forms[g_FormName];
        if (form) {
            let field = form.elements[g_FieldName];
            if (field) {
                let l_SetFocus = true;
                if (g_FormName == 'AccountLogin' && document.getElementsByName('loginemail')[0].value.length > 0) {
                    l_SetFocus = false;
                }
                if (l_SetFocus == true) {
                    field.focus();
                }
            }
        }
    }
}

function SetFormFocusToArguments(a_FormName, a_FieldName) {
    if (a_FormName.length > 0 && a_FieldName.length > 0) {
        document.forms[a_FormName].elements[a_FieldName].focus();
        document.forms[a_FormName].elements[a_FieldName].focus();
        document.forms[a_FormName].elements[a_FieldName].focus();
        document.forms[a_FormName].elements[a_FieldName].blur();
        document.forms[a_FormName].elements[a_FieldName].blur();
        document.forms[a_FormName].elements[a_FieldName].blur();
    }
}

function ToggleMaskedText(a_TextFieldID) {
    m_DisplayedText = document.getElementById('Display' + a_TextFieldID).innerHTML;
    m_MaskedText = document.getElementById('Masked' + a_TextFieldID).innerHTML;
    m_ReadableText = document.getElementById('Readable' + a_TextFieldID).innerHTML;
    if (m_DisplayedText == m_MaskedText) {
        document.getElementById('Display' + a_TextFieldID).innerHTML = document.getElementById('Readable' + a_TextFieldID).innerHTML;
        document.getElementById('Button' + a_TextFieldID).src = JS_DIR_IMAGES + 'global/general/hide.gif';
    } else {
        document.getElementById('Display' + a_TextFieldID).innerHTML = document.getElementById('Masked' + a_TextFieldID).innerHTML;
        document.getElementById('Button' + a_TextFieldID).src = JS_DIR_IMAGES + 'global/general/show.gif';
    }
}

function FormatDate(a_DateTimeStamp, a_Format) {
    l_Format = 'full';
    l_Date = new Date(a_DateTimeStamp);
    l_Output = l_Date.toString().substring(4);
    return l_Output;
}

function RedirectGET(a_Target) {
    window.location = decodeURIComponent(a_Target);

}

var PostParameters = new Object();

function RedirectPOST(a_Target, a_ParameterArray) {
    $('<form />').hide().attr({ method: "post", id: "RedirectForm" }).attr({ action: decodeURIComponent(a_Target) }).append('<input type="submit" />').appendTo($("body"));
    $.each(a_ParameterArray, function(key, value) {
        console.log(key + ": " + value);
        $('#RedirectForm').append($('<input />').attr("type", "hidden").attr({ "name": key }).val(value));
    });
    $('#RedirectForm').submit();
}
var g_CurrentScreenshot = 0;
var g_NumberOfScreenshots = 0;
var g_Screenshots = new Array();
var g_ScreenshotTexts = new Array();

function SetScreenshot(a_Number) {
    g_CurrentScreenshot = a_Number;
    $("#ScreenshotContainer").fadeTo("fast", 0, function() {
        $('#ScreenshotImage').attr('src', g_Screenshots[g_CurrentScreenshot].src);
        $('.ScreenshotTextRow').text(g_ScreenshotTexts[g_CurrentScreenshot]);
        $("#ScreenshotContainer").fadeTo("fast", 1, function() {});
    });
}

function ShowNextScreenshot() {
    g_CurrentScreenshot = (g_CurrentScreenshot + 1);
    if (g_CurrentScreenshot > g_NumberOfScreenshots) {
        g_CurrentScreenshot = 1;
    }
    SetScreenshot(g_CurrentScreenshot);
}

function ShowPreviousScreenshot() {
    g_CurrentScreenshot = (g_CurrentScreenshot - 1);
    if (g_CurrentScreenshot < 1) {
        g_CurrentScreenshot = g_NumberOfScreenshots;
    }
    SetScreenshot(g_CurrentScreenshot);
}

function ShowScreenshot(a_Number, a_NumberOfScreenshots) {
    g_NumberOfScreenshots = a_NumberOfScreenshots;
    g_CurrentScreenshot = a_Number;
    $("#LightBox").fadeTo("fast", 1, function() {});
    $("#LightBoxBackground").fadeTo("fast", 0.75, function() {});
    SetScreenshot(a_Number);
}

function PreloadScreenshots(a_NumberOfScreenshots) {
    for (i = 1; i <= a_NumberOfScreenshots; i++) {
        g_Screenshots[i] = new Image();
        g_Screenshots[i].src = JS_DIR_IMAGES + 'abouttibia/tibia_screenshot_' + i + '.png'
    }
}

function ImageInNewWindow(a_ImageSource) {
    const l_Image = new Image();
    var l_WindowWidth = 100;
    var l_WindowHeight = 100;
    var l_NewWindow = window.open('', '', 'width=' + l_WindowWidth + ', height=' + l_WindowHeight);
    l_Content = '<body style="margin: 0px; background: #0e0e0e; height: 100%; display: flex; align-items: center; justify-content: center;" >';
    l_Content += '<img src="' + a_ImageSource + '" />';
    l_Content += '</body>';
    l_NewWindow.document.write(l_Content);
    l_Image.onload = function() {
        l_BorderWidth = (l_NewWindow.outerWidth - l_NewWindow.innerWidth);
        l_BorderHeight = (l_NewWindow.outerHeight - l_NewWindow.innerHeight);
        var l_PixelSpace = 5;
        var l_WindowWidth = (l_Image.naturalWidth + l_PixelSpace + l_BorderWidth);
        var l_WindowHeight = (l_Image.naturalHeight + l_PixelSpace + l_BorderHeight);
        l_NewWindow.resizeTo(l_WindowWidth, l_WindowHeight);
        l_NewWindow.focus();
    }
    l_Image.src = a_ImageSource;
}