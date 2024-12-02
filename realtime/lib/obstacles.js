let database = require("./pipMysql.js");
let kwbot = database.WebApp("kwbot");
class dataTo{
    constructor(val = 100){
        let _val = val;
        let _path = 0;
        let _loc = 1;
        let _veh = 0;
        
        
        this.val = function(v){
            _val = v;
            return this;
        }
        
        this.path = function(v){
            _path = v;
            return this;
        }
        this.loc = function(v){
            _loc = v;
            return this;
        }
        this.veh = function(v){
            _veh = v;
            return this;
        }
        
        this.values = function(index = 1){
            return [_val,index,_path,_loc,_veh];
        }
        this.mValues = function(index = 1){
            return [_val,index,_veh];
        }
    }
}

class ultrasonic{
    constructor(type = 1){
        if(type<1) type = 1;
        if(type>8) type = 8;
        this.type = type;
        let t = -10;
        let v = 101;
        this.time = function(){
            return t;
        }
        
        this.val = function(){
            return v;
        }
        
        this.set = function(vv,tt){
            t = tt;
            v = vv;
            return this;
        }
        
    }
}

function obst(v = 100){
    return new dataTo(v);
}

function values(da,index){
    return [da.val,1,da.path,da.loc,da.veh];
}

class obstacles{
    constructor(){
        const MAX_SAMP = 1000;
        let  map_ = false;
        this.map = function(){
            return map_;
        }
        this.obst = {
            field:["distance","ultra_id","path_id","path_loc","vehicles_id"]
        }
        this.Mobst = {
            field:["distance","ultra_id","vehicles_id"]
        }
        let ultra_ = database.admin("ultra","ultra_id",kwbot.connection());
        let obstacles_ = database.admin("obstacles","obstacles_id",kwbot.connection());
        let obstacles_map_ = database.admin("obstacles_map","obstacles_map_id",kwbot.connection());
        
        let last1000 = {
            index:["FR","FL","BR","BL","RF","RB","LF","LB"],
            data:{
                "FR":new Array(MAX_SAMP),
                "FL":new Array(MAX_SAMP),
                "BR":new Array(MAX_SAMP),
                "BL":new Array(MAX_SAMP),
                "RF":new Array(MAX_SAMP),
                "RB":new Array(MAX_SAMP),
                "LF":new Array(MAX_SAMP),
                "LB":new Array(MAX_SAMP)
            },
            i:0
        }
        
        let getIndex = function(i){
            if(i<1) i = 1;
            if(i>8) i = 8;
            return last1000.index[i-1];
        }
        
        for(var i = 0; i < MAX_SAMP; i++){
            
            last1000.data.FR[i] = new ultrasonic();
            last1000.data.FL[i] = new ultrasonic(2);
            
            last1000.data.BR[i] = new ultrasonic(3);
            last1000.data.BL[i] = new ultrasonic(4);
            
            last1000.data.RF[i] = new ultrasonic(5);
            last1000.data.RB[i] = new ultrasonic(6);
            
            last1000.data.LF[i] = new ultrasonic(7);
            last1000.data.LB[i] = new ultrasonic(8);
            
        }
        
        this.add = function(ULTRASON){
            if(last1000.i<MAX_SAMP-1) last1000.i++;
            last1000.data[getIndex(ULTRASON.type)][last1000.i].set(ULTRASON.val(),)
        }
        
        this.last = function(){
            
        }
        
        
        
        this.ultra = function(){
            return ultra_;
        }
        
        this.obstacles = function(){
            return obstacles_;
        }
        
        this.obstacles_map = function(){
            return obstacles_map_;
        }
    }
    // all functions to add obstacle detected values to the database during path
    aFR(da,fun){
        this.obstacles().add( fun, this.obst.field,da.values());
    }
    aFL(da,fun){
        this.obstacles().add( fun, this.obst.field,da.values(2));
    }
    aBR(da,fun){
        this.obstacles().add( fun, this.obst.field,da.values(3));
    }
    aBL(da,fun){
        this.obstacles().add( fun, this.obst.field,da.values(4));
    }
    aRF(da,fun){
        this.obstacles().add( fun, this.obst.field,da.values(5));
    }
    aRB(da,fun){
        this.obstacles().add( fun, this.obst.field,da.values(6));
    }
    aLF(da,fun){
        this.obstacles().add( fun, this.obst.field,da.values(7));
    }
    aLB(da,fun){
        this.obstacles().add( fun, this.obst.field,da.values(8));
    }
    // all functions to add obstacle detected values to the database during maping
    amFR(da,fun){
        this.obstacles().add( fun, this.Mobst.field,da.mValues());
    }
    amFL(da,fun){
        this.obstacles().add( fun, this.Mobst.field,da.mValues(2));
    }
    amBR(da,fun){
        this.obstacles().add( fun, this.Mobst.field,da.mValues(3));
    }
    amBL(da,fun){
        this.obstacles().add( fun, this.Mobst.field,da.mValues(4));
    }
    amRF(da,fun){
        this.obstacles().add( fun, this.Mobst.field,da.mValues(5));
    }
    amRB(da,fun){
        this.obstacles().add( fun, this.Mobst.field,da.mValues(6));
    }
    amLF(da,fun){
        this.obstacles().add( fun, this.Mobst.field,da.mValues(7));
    }
    amLB(da,fun){
        this.obstacles().add( fun, this.Mobst.field,da.mValues(8));
    }
}

//let ab = new obstacles();
//
//ab.aFR(obst(30)
//       .path(1)
//       .loc(30)
//       .veh(2), function(){
//            console.log("saved success..."); 
//});