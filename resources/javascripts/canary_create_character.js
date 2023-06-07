let timeout;
let password = document.getElementById('password1')
let password2 = document.getElementById('password2')
let strengthBadge = document.getElementById('StrengthDisp')
let PWRule01 = document.getElementById('PWRule01')
let PWRule02 = document.getElementById('PWRule02')
let PWRule03 = document.getElementById('PWRule03')
let PWRule04 = document.getElementById('PWRule04')
let PWRule05 = document.getElementById('PWRule05')
let PWRule06 = document.getElementById('PWRule06')
let PWStrengthIndicator = document.getElementById('PWStrengthIndicator')
let password1_indicator = document.getElementById('password1_indicator')
let password1_label = document.getElementById('password1_label')
let password2_label = document.getElementById('password2_label')
let password_errormessage = document.getElementById('password_errormessage')

let accname = document.getElementById('accname')
let accname_indicator = document.getElementById('accname_indicator')
let accname_label = document.getElementById('accname_label')
let accname_regex = /^[a-zA-Z0-9_\-@]{5,}$/;
let accname_errormessage = document.getElementById('accname_errormessage')

let email = document.getElementById('email')
let email_indicator = document.getElementById('email_indicator')
let email_label = document.getElementById('email_label')
let email_regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
let email_errormessage = document.getElementById('email_errormessage')

let passwordConst = document.getElementById('password1')
let invalida = /[ \f\n\r\t\v\u00A0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u2028\u2029\u2028\u2029\u202f\u205f\u3000]/;
let especial = /[@!#$%^&*()/\\]/;
let minuscula = /[a-z]/;
let maiuscula = /[A-Z]/;
let numberPassword = /[0-9]/;
let indicator = 0;
let strongPassword = new RegExp('(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})')
let mediumPassword = new RegExp('((?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{6,}))|((?=.*[a-z])(?=.*[A-Z])(?=.*[^A-Za-z0-9])(?=.{8,}))')

function StrengthChecker(PasswordParameter) {
    if (password.value.length >= 10) {
        PWRule01.classList.remove('InputIndicatorNotOK')
        PWRule01.classList.add('InputIndicatorOK')
        indicator++;
    } else {
        PWRule01.classList.remove('InputIndicatorOK')
        PWRule01.classList.add('InputIndicatorNotOK')
    }

    if (invalida.test(PasswordParameter)) {
        PWRule02.classList.remove('InputIndicatorOK')
        PWRule02.classList.add('InputIndicatorNotOK')
    } else {
        PWRule02.classList.remove('InputIndicatorNotOK')
        PWRule02.classList.add('InputIndicatorOK')
        indicator++;
    }

    if (especial.test(PasswordParameter)) {
        PWRule03.classList.remove('InputIndicatorNotOK')
        PWRule03.classList.add('InputIndicatorOK')
        indicator++;
    } else {
        PWRule03.classList.remove('InputIndicatorOK')
        PWRule03.classList.add('InputIndicatorNotOK')
    }

    if (minuscula.test(PasswordParameter)) {
        PWRule04.classList.remove('InputIndicatorNotOK')
        PWRule04.classList.add('InputIndicatorOK')
        indicator++;
    } else {
        PWRule04.classList.remove('InputIndicatorOK')
        PWRule04.classList.add('InputIndicatorNotOK')
    }

    if (maiuscula.test(PasswordParameter)) {
        PWRule05.classList.remove('InputIndicatorNotOK')
        PWRule05.classList.add('InputIndicatorOK')
        indicator++;
    } else {
        PWRule05.classList.remove('InputIndicatorOK')
        PWRule05.classList.add('InputIndicatorNotOK')
    }

    if (numberPassword.test(PasswordParameter)) {
        PWRule06.classList.remove('InputIndicatorNotOK')
        PWRule06.classList.add('InputIndicatorOK')
        indicator++;
    } else {
        PWRule06.classList.remove('InputIndicatorOK')
        PWRule06.classList.add('InputIndicatorNotOK')
    }

    if (password.value.length > 0) {
        PWStrengthIndicator.textContent = 'very weak';
        PWStrengthIndicator.classList.remove('PWStrengthLevel0')
        PWStrengthIndicator.classList.add('PWStrengthLevel1')
    }

    if (password.value.length > 5) {
        PWStrengthIndicator.textContent = 'weak';
        PWStrengthIndicator.classList.remove('PWStrengthLevel1')
        PWStrengthIndicator.classList.add('PWStrengthLevel2')
    }

    if (password.value.length >= 10 && !invalida.test(PasswordParameter) && especial.test(PasswordParameter) && minuscula.test(PasswordParameter) && maiuscula.test(PasswordParameter) && numberPassword.test(PasswordParameter)) {
        PWStrengthIndicator.textContent = 'medium';
        PWStrengthIndicator.classList.remove('PWStrengthLevel2')
        PWStrengthIndicator.classList.add('PWStrengthLevel3')
    }

    if (password.value.length >= 12 && !invalida.test(PasswordParameter) && especial.test(PasswordParameter) && minuscula.test(PasswordParameter) && maiuscula.test(PasswordParameter) && numberPassword.test(PasswordParameter)) {
        PWStrengthIndicator.textContent = 'strong';
        PWStrengthIndicator.classList.remove('PWStrengthLevel3')
        PWStrengthIndicator.classList.add('PWStrengthLevel4')
        password1_indicator.classList.remove('InputIndicatorNotOK')
        password1_indicator.classList.add('InputIndicatorOK')
        password_errormessage.style.display = 'none'
    }
}
password.addEventListener("input", () => {
    password1_indicator.classList.remove('InputIndicatorOK')
    password1_indicator.classList.add('InputIndicatorNotOK')
    password_errormessage.style.display = 'flex'

    strengthBadge.style.display = 'block'
    StrengthChecker(password.value)
    if (password.value.length !== 0) {
        strengthBadge.style.display != 'block'
    } else {
        strengthBadge.style.display = 'none'
    }
});

function PasswordTwo(one, two) {
    if (password2.value == password.value) {
        password2_indicator.classList.remove('InputIndicatorNotOK')
        password2_indicator.classList.add('InputIndicatorOK')
        password2_label.style.color = ''
        password_errormessage.style.display = 'none'
    }
}
password2.addEventListener("input", () => {
    password2_indicator.classList.remove('InputIndicatorOK')
    password2_indicator.classList.add('InputIndicatorNotOK')
    password2_label.style.color = 'red'
    password_errormessage.style.display = 'flex'
    PasswordTwo(password.value, password1.value)
});

function isEmail(EmailParameter) {
    if (email_regex.test(EmailParameter)) {
        email_indicator.classList.remove('InputIndicatorNotOK')
        email_indicator.classList.add('InputIndicatorOK')
        email_label.style.color = ''
        email_errormessage.style.display = 'none'
    } else {
        email_indicator.classList.remove('InputIndicatorOK')
        email_indicator.classList.add('InputIndicatorNotOK')
        email_label.style.color = 'red'
        email_errormessage.style.display = 'flex'
    }
}

function isAccname(AccnameParameter) {
    if (accname_regex.test(AccnameParameter)) {
        accname_indicator.classList.remove('InputIndicatorNotOK')
        accname_indicator.classList.add('InputIndicatorOK')
        accname_label.style.color = ''
        accname_errormessage.style.display = 'none'
    } else {
        accname_indicator.classList.remove('InputIndicatorOK')
        accname_indicator.classList.add('InputIndicatorNotOK')
        accname_label.style.color = 'red'
        accname_errormessage.style.display = 'flex'
    }
}

accname.addEventListener("input", () => {
    accname_indicator.classList.remove('InputIndicatorOK')
    accname_indicator.classList.add('InputIndicatorNotOK')
    isAccname(accname.value)
});

email.addEventListener("input", () => {
    email_indicator.classList.remove('InputIndicatorOK')
    email_indicator.classList.add('InputIndicatorNotOK')
    isEmail(email.value)
});