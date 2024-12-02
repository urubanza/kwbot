var mysql = require('mysql');
 /* 
 file has been composed by Pacifique Ishimwe a.k.a PIP for commercial and serious issue
    please check lisence before using this. copyright PIP allright reserved.
    ################################################################################################################
  PIP is an application software development libray available for Core PHP/MySql, Core Javascript( DOM for web application) & Core Javascript ( Node for Serverside fullduplexed com. server), x86 & x64 assembly with C/C++ and python, Objective C/Swift( for IOS applications ) ,Java/Kotlin for android applications, CLI/C# .NET for Windows.
  And is for those who need to create great and innovative system and application softwares  designed for IoT, Artificial intergence, blockchain applications and many more advanced engeenering software both application and system softwares.
**/
// a class to keep constants of the root server
class rootConfig{
    constructor(folder = ""){
       this.ROOT =  rootConfig.ROOT_S(folder);
        this.ROOT.LOCATION = "http://"+this.ROOT.SERVER+"/";
        if(!(this.ROOT.FORDER==="")){
            this.ROOT.LOCATION = this.ROOT.LOCATION+this.ROOT.FORDER+"/";
        }
        
        this.ROOT.IMG = this.ROOT.LOCATION +"img/";
    }
    static ROOT_S(folder = ""){
        return {
            SERVER:"192.168.43.40",
            FORDER:folder,
            USERNAME:"root",
            PASSWORD:"",
            HOST:"localhost",
            ERROR:""
        };
    }
    conn(database){
        this.ROOT.CONNECTION = mysql.createConnection({
                                  host: "localhost",
                                  user: this.ROOT.USERNAME,
                                  password: this.ROOT.PASSWORD,
                                  database: database
                               });
        var THIS = this;
        this.ROOT.CONNECTION.connect(function(err) {
              if (err) {
                 console.log("failed to connect to the root database, this will results failure of configuration");
                 throw err;
              } else {
                  THIS.ROOT.CONNECTION.end();
                  console.log("the main Mysql server Connected success!");
              }
        });
        this.ROOT.CONNECTION.end();
        return this.ROOT.CONNECTION;
    }
    rootF(index,EXACT){
        if(eval("this.ROOT."+index)){
            console.log(eval("this.ROOT."+index));
            return eval("this.ROOT."+index);
        } else {
            console.log("Unkown index ("+index+") !")
        }
    }
    imageg(name){
        return this.ROOT.IMG+name;
    }
}
// a siimplified function to return the rootConfig object
function root(folder){
    return new rootConfig(folder);
}
class webApp{
    constructor(argument, host = "", password = "", username = "", struct = ""){
        this.Structure = struct;
        this.database = argument;
        this.err = false;
        this.message = "no thing wrong yet!";
        this.conn = 0;
        if(host===""){
            this.con = {
                host:rootConfig.ROOT_S().HOST,
                db:this.database,
                user:rootConfig.ROOT_S().USERNAME,
                password:rootConfig.ROOT_S().PASSWORD,
                status:"waiting"
            }
        } else {
            this.con = {
                host:host,
                db:this.database,
                user:username,
                password:password,
                status:"waiting"
            }
        }
        
    }
    _connection(){
        return this.con;
    }
    _connection_(FUN){
        this.conn = mysql.createConnection({
                      host:this.con.host,
                      user: this.con.user,
                      password: this.con.password,
                      database: this.con.db,
                   });
        var THIS = this;
        conn.connect( function(err){
            if(err){
                console.log("Connection to the mysql server failed with the "+THIS.con.db+" name");
                throw(err);
            } else {
                THIS.message = "Connection to the MySql Server initiated success";
                THIS.conn.end();
                FUN(THIS);
            }
        })
    }
    connection(host,password,username){
        this.con.host = host;
        this.con.password = password;
        this.con.user = username;
        return this.con;
    }
    getError(){
        return this.message;
    }
    set_message(str){
        this.message = str;
    }
    table(name,id = ""){
        var primary_key = id;
        if(id===""){
            primary_key = name+"_id";
        }
        var table = new admin(name,primary_key);
        return table.con_(this.con);
    }
    client(name){
        new PIPCLIENTS(name,this.con);
    }
    close(FUN){
        var THIS = this;
        if(!(this.conn===0)){
           this.conn.end( function(err){ 
                if(err) throw err;
                else {
                   THIS.conn = 0;
                   FUN(THIS);
                }
            }); 
        } else {
            FUN(THIS);
        }
    }
    open(FUN){
        this.conn = mysql.createConnection({
				  host: this.con.host,
				  user: this.con.user,
				  password: this.con.password,
                  database: this.con.db
				});
         var THIS = this;
         this.conn.connect(function(err) {
              if (err) {
                  THIS.conn.close;
                  THIS.message = "Opening of "+this.database+" failed";
                  THIS.err = true;
                  throw err;
              } else {
                  THIS.err = false;
                  console.log("Connected success!");
              }
              FUN(THIS);
         });
    }
    create(FUN){
        this.open( function(THIS){
            THIS.conn.query(THIS.Structure, function(err, result){
               if(err){
                   THIS.message = "Object initiation failed ): "+err.toString();
                   THIS.err = true;
               } else {
                   THIS.message = "Object initiation success (: ";
                   THIS.err = false;
               } 
               FUN(THIS);
            })
        });
    }
    initial(struct){
        this.Structure = struct;
        return this;
    }
}
class PIPCLENTS extends webApp{
    constructor(argument,con){
        
    }
}

class PIP_Array {
    constructor(DATAS, fieldsN = []){
        this.AV = new Array(DATAS.length);
        this.RV = new Array(DATAS.length);
        this.fields = new Array(fieldsN.length);
        var i = DATAS.length;
        for(var ii = 0; ii < DATAS.length;ii++){
            this.AV[ii] = DATAS[ii];
            this.RV[i-1] = DATAS[ii];
            i--;
        }
        
        for(var ii = 0; ii< fieldsN.length; ii++){
            if(fieldsN[ii].name)
                this.fields[ii] = fieldsN[ii].name;
            else this.fields[ii] = fieldsN[ii];
        }
        this.size = DATAS.length;
        for(var ii = 0; ii< this.fields.length; ii++){
            for(var iii=0;iii<this.size;iii++){
                this.AV[iii][ii] = this.AV[iii][this.fields[ii]];
                this.RV[iii][ii] = this.RV[iii][this.fields[ii]];
            }
        }
        
    }
    _gets_(ind,val = "",type="REMOVE"){
       var DATS = [];
        if(parseInt(ind)){
            return this._gets_(this.fields[0],ind,"O");
        } else {
        for(var t =0 ; t<this.size;t++){
            if(type==="REMOVE"){
               if(!(this.AV[t][ind]===val)){
                    DATS.push(this.AV[t]);
                }
            } else {
               if(this.AV[t][ind]===val){
                    DATS.push(this.AV[t]);
                } 
            }
        }
        return new PIP_Array(DATS, this.fields);
       }
    }
    getsD(INDEX){
        var rets = new Array();
        for(var ii=0;ii<this.size;ii++){
            var exist = false;
            for(var iii=0;iii<ii;iii++){
                if(this.AV[ii][INDEX]==this.AV[iii][INDEX]){
                    exist = true;
                }
            }
            if(!exist) rets.push(this.AV[ii]);
        }
        return new PIP_Array(rets);
    }
    getsM(theArray2,ind1,ind2,type="REMOVE"){
        if(theArray2.size>0){
             for(var ii=0;ii<all.size;ii++){
                   ARR = ARR._gets_(ind2,all.AV[ii][ind1],type);
             }
             return new PIP_Array(ARR.AV, this.fields);
        } else return this.empty();
         
    }
    getsF(theArray2,ind1,ind2,type="REMOVE"){
        var ARR = this.getsD(ind2);
        if(theArray2.size>0){
             var all = theArray2.getsD(ind1);
             for(var ii=0;ii<all.size;ii++){
                   ARR = ARR._gets_(ind2,all.AV[ii][ind1],type);
             }
             return new PIP_Array(ARR.AV, this.fields);
        } else return this.empty();
    }
    empty(){
        return new PIP_Array(new Array(), this.fields);
    }
    lowest(INDEX = 0){
         var lowest = this.AV[0][INDEX];
         for(var ii=0; ii<this.size; ii++){
             if(this.AV[ii][INDEX]<lowest){
                 lowest = this.AV[ii][INDEX];
             }
         }
        return lowest;
    }
    lowestP(INDEX = 0){
         return this._gets_(INDEX,this.lowest(INDEX),"O");
    }
    highest(INDEX = 0){
         var highest = this.AV[0][INDEX];
         for(var ii=0;ii<this.size;ii++){
             if(this.AV[ii][INDEX]>highest){
                 highest = this.AV[ii][INDEX];
             }
         }
        return highest;
     }
    highestP(INDEX = 0){
         return this._gets_(INDEX,this.highest(INDEX),"O");
    }
    included(INDEX,VALUE){
         var rets = false;
         for(var ii=0;ii<this.size;ii++){
             if(this.AV[ii][INDEX]){
                 if(this.AV[ii][INDEX]==VALUE){
                     rets = true;
                     break;
                 }
             }
         }
         return rets;
    }
    lessThan(value,index = 0){
        var rets = new Array();
        for(var ii=0;ii<this.size;ii++){
            if(parseInt(value)>parseInt(this.AV[ii][index])){
               rets.push(this.AV[ii]);
            }
        }
        return new PIP_Array(rets,this.fields);
     }
    JS(index){
        return this.AV[index];
    }
}

class admin extends webApp {
    constructor(tablename,primaryKey = "",conns = 0){
        super();
        this.table_name = tablename;
        this.primary_key = primaryKey;
        this.connecting = false;
        if((conns.host)&&(conns.db)&&(conns.user)&&(conns.password)){
            this.con(conns);
        } 
        else {
            this.con = {
                host:rootConfig.ROOT_S().HOST,
                db:rootConfig.ROOT_S().FORDER,
                user:rootConfig.ROOT_S().USERNAME,
                password:rootConfig.ROOT_S().PASSWORD,
                status:"waiting..."
            }
        }
        this.conn = 0;
        if(primaryKey===""){
            this.primary_key = tablename+"_id";
        }
        this.sql = "SELECT * FROM `"+this.table_name+"` WHERE 1";

    }
    SELECT_DATAS( FUN ){
        this.open( function(adm){
            adm.conn.query(adm.sql, function(err, result, fields){
                if(err) throw err;
                adm.close( function(adm2){
                    var rets = new PIP_Array(result, fields);
                    FUN(rets);
                });
            });
        });
    }
    _gets_(FUN, fields = [], values = [] , conds = [] , order = "", start = -1 , _lenght = 0 ){
        this.sql = "SELECT * FROM `"+this.table_name+"`";
        if((Array.isArray(fields))&&(Array.isArray(values))&&(Array.isArray(conds))){
                if(fields.length===values.length){
                   if((fields.length-1)<=conds.length){
                       for(var i=0; i<fields.length;i++){
                           if(!i){
                               this.sql = this.sql+" WHERE `"+fields[i]+"` = '"+values[i]+"' ";
                           } else {
                               this.sql = this.sql+" "+conds[i-1]+" `"+fields[i]+"` = '"+values[i]+"' ";
                           }
                       }
                       
                       if(!(order=="")){
                            if(order=="RAND"){
                                order = "RAND()";
                            }
                            this.sql = this.sql+" ORDER BY `"+order+"` ASC ";
                            if(!(start<0)&&(_lenght>0))
                                this.sql = this.sql + " LIMIT "+start+","+_lenght+" ";
                       } else {
                           this.sql = this.sql+" ORDER BY `"+this.primary_key+"` ASC ";
                           if(!(start<0)&&(_lenght>0))
                                this.sql = this.sql + " LIMIT "+start+","+_lenght+" ";

                       }
                       
                   } else {
                      this.set_message("Error : less number of logical operators. fields: " + fields.length + " conditions: "+conds.length);
                      console.log(this.message); 
                   }
                } else {
                    this.set_message("Error : number of fields and number of values must be equal. fields: " + fields.length + " values: "+values.length);
                      console.log(this.message);
                }
        } 
        else {
            console.log(200);
        }
        return this.SELECT_DATAS(FUN);
     }
    add(FUN,fields,values,datefileds=""){
        var statatus = 0;
 	    var fieldsX = "";
 	    var valuesX = "";
        if(fields.length == values.length){
            for(var ii=0;ii<fields.length;ii++){
                fieldsX +=",`"+fields[ii]+"`";
                valuesX +=",'"+values[ii]+"'";
            }
            
            if(!(datefileds=="")){
                fieldsX = fieldsX + ", `"+datefileds+"`";
                valuesX = valuesX + ", CURRENT_TIMESTAMP";
            }
            this.sql = "INSERT INTO `"+this.table+"` (`"+this.primary_key+"` "+fieldsX+") VALUES (NULL "+valuesX+")";
            
            this.open( function(tab){
               tab.conn.query(tab.sql, function(err, result){
                   if(err) throw err;
                   tab.close( function(tab2){
                       tab2.err = false;
                       tab2.set_message(" all records has been added success ");
                       FUN(tab2);
                   })
               }) 
            });
        }
        else {
            this.err = true;
            this.set_message(" Number of fields must equal to the number of values ! ");
            FUN(this);
        }
    }
    delete(FUN,values,fields = ""){
        var fields_ = this.primary_key;
        if(!(fields==""))
            fields_ = fields;
        this.sql = "DELETE FROM `"+this.table+"` WHERE `"+this.table+"`.`"+fields_+"` = "+values;
        this.open( function(tab){
            tab.conn.query(tab.sql, function(err, result){
                if(err) throw err;
                tab.close( function(tab2){
                    tab2.err = false;
                    tab2.set_message(" Records failed to be removed");
                    FUN(tab2);
                })
            })
        })
    }
    edit(FUN,values,fields,id){
        this.sql = "UPDATE `"+this.table+"`  SET "
        
        for(var cc = 0; cc<values.length; cc++){
            if(cc>0)
              this.sql += ",";
              this.sql += "`"+fields[cc]+"` = `"+values[cc]+"`"
            
        }
        
        this.sql += "WHERE `"+this.table+"`.`"+this.primary_key+"` = "+id;
        this.open( function(tab){
            tab.conn.query(tab.sql, function(err, result){
                if(err) throw err;
                this.close( function(tab2){
                    tab2.err = false;
                    tab2.message = "Data updated success";
                })
            })
        });
    }
    search(FUN, WHERE = [], KEYWORDS, FIELDS = [], VALUES = []){
        this.sql = "SELECT * FROM `"+this.table+"` WHERE";
        
        for(var i = 0; i < WHERE.length; i++){
            if(i){
                this.sql += " OR "
            }
            this.sql += "`"+WHERE[i]+"` LIKE '%"+KEYWORDS+"%'";  
        }
        
        for(var i = 0; i < FIELDS.length; i++){
            this.sql += " AND "+ FIELDS[i]+" = "+VALUES[i];
        }
        this.sql += " ORDER BY `"+this.primary_key+"` DESC";
        return this.SELECT_DATAS(FUN);
    }
    con_(con){
        this.con = con;
        this.database = this.con.db;
        return this;
    } 
}




exports.root = (folder)=>{
    return new rootConfig(folder);
}
exports.database = function(argument,host = "",password = "",username = "",struct = ""){
    return new webApp(argument,host,password,username,struct); 
}
exports.CLIENT_LIST = function(ACCOUNTS_names,conn){
    var ACCOUNTS = {};
    for(var ii=0;ii<ACCOUNTS_names.length;ii++){
      eval("ACCOUNTS."+ACCOUNTS_names[ii]+" = new PIPCLENTS("+ACCOUNTS_names[ii]+",conn)");
    }
    return ACCOUNTS;
}
