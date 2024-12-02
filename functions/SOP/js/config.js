// definition of the required root configuration of the whole project
 var ROOT = {
    "SERVER":"192.168.1.77",
    "FORDER":"kwbot",
    "USER_NAME":"",
    "PASSWORD":"",
    "HOST":"localhost",
    "STORAGE":"kwbot",
    "ERROR":"",
    "MAIN_PROTACAL":"http"
}

ROOT.LOCATION = ROOT.MAIN_PROTACAL+"://"+ROOT.SERVER+"/"+ROOT.FORDER;
ROOT.IMG = ROOT.LOCATION+"/img/";


// definition of required images of the whole project
var LOADING_S_ = new Image();
var LOADING_N_ = new Image();
LOADING_S_.setAttribute("class","loading-and-waiting-img loading-and-waiting-img-small");
LOADING_N_.setAttribute("class","loading-and-waiting-img loading-and-waiting-img-normal");

LOADING_S_.style.position = "relative";
LOADING_S_.style.left = "40%";
LOADING_N_.style.position = "relative";
LOADING_N_.style.left = "50%";

LOADING_S_.src = ROOT.IMG+"sys_files/loading.gif";
LOADING_N_.src = ROOT.IMG+"sys_files/loading.gif";

//the alternative function to return the image to simplify the LOADING text

function l(){
    return LOADING_S_;
}

function L(){
    return LOADING_N_;
}
