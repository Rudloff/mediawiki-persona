/*jslint browser: true */
var initLogin = function () {
    "use strict";
    var login, connect, loginBtn;
    login = function (assertion) {
        if (assertion) {
            var form, postvar;
            form = document.createElement('form');
            form.setAttribute('method', 'post');
            postvar = document.createElement('input');
            postvar.setAttribute('type', 'hidden');
            postvar.setAttribute('name', "assertion");
            postvar.setAttribute('value', assertion);
            form.appendChild(postvar);
            document.body.appendChild(form);
            form.submit();
        }
    };
    connect = function () {
        navigator.id.get(login);
    };
    loginBtn = document.getElementById("login");
    if (loginBtn.addEventListener) {
        loginBtn.addEventListener("click", connect, false);
    } else if (loginBtn.attachEvent) {
        loginBtn.attachEvent("onclick", connect);
    }
};
if (window.addEventListener) {
    window.addEventListener("load", initLogin, false);
} else if (window.attachEvent) {
    window.attachEvent("onload", initLogin);
}
