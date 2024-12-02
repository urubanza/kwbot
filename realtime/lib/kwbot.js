let database = require("./pipMysql.js");
let kwrobot = database.WebApp("kwbot");
let paths  = require("./path");

let type_ = database.admin("robots_type","robots_type_id",kwrobot.connection());
let vehicle = database.admin("vehicles","vehicles_id",kwrobot.connection());
let vehicles_t = database.admin("vehicles","vehicles_type_id",kwrobot.connection());
let vehicles_logs = database.admin("vehicles_logs","vehicles_logs_id",kwrobot.connection());
let pathsx = database.admin("path","path_id",kwrobot.connection());

exports.types = function(){
	return type_;
}

exports.vehicles = function(){
	return vehicle;
}

exports.vehicles_type = function(){
	return vehicles_t;
}

exports.pathxx = database.admin("path","path_id",kwrobot.connection());

exports.logs = function(){
	return vehicles_logs;
}

exports.path = ()=>{
    return paths.path();
}
exports.timer = ()=>{
    return paths.timer();
}



