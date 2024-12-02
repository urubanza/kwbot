var mysql = require('mysql');
var currentDatabaseName = '';
var defaultUser  = "root";
var defaultPassword = "";
var defaultHost = "localhost";

class WebApp {
    //message = "";
    constructor(dataBase){
        let database = dataBase;
        let error_message = "nothing wrong yet";
        let err = false;
        this.message = () => {
            return error_message;
        }
        
        this.getDatabase = () => {
            return database;
        }
        this.error = () =>{
            return err;
        }
        
        this.setMessage = (msg , bools = false ) =>{
            error_message = msg;
            err = bools;
        }
        
        var connecti = {
            hostName : defaultHost,
            userName : defaultUser,
            password : defaultPassword,
            database: this.getDatabase()
        }
        
        this._connection = (HOST,USER,PASSWORD) =>{
            connecti.hostName = HOST;
            connecti.userName = USER;
            connecti.password = PASSWORD;
            
        }
        this.connection = () =>{
            return connecti;
        }
    }
    
    connect(hosts,passwords,usernames , FUN){
        this.setMessage(" waiting for mysql connection :"+ this.getDatabase(), false);
        this._connection(hosts,usernames,passwords);
        var WEBAPP = this;
        this.conn = mysql.createConnection({
				  host: hosts,
				  user: usernames,
				  password: passwords,
                  database: DATABASE
				});
        
        WEBAPP.conn.connect(function(err) {
              if (err) {
                  WEBAPP.setMessage("enable to connect to the database :"+ WEBAPP.getDatabase(), false);
                  FUN();
                  return false;
              } else {
                  WEBAPP.setMessage(" Connection established to"+ WEBAPP.getDatabase() +" successfully !!", true);
                  FUN();
                  return WEBAPP.connecti;
              }
        });
        
        
    }
    _connect( FUN ){
        this.setMessage(" waiting for mysql connection :"+ this.getDatabase(), false);
        var DATABASE = this.database;
        var connecti = {
            hostName : defaultHost,
            userName : defaultHost,
            password : defaultPassword,
            database: DATABASE
        }
        this.conn = mysql.createConnection({
				  host: defaultHost,
				  user: defaultUser,
				  password: defaultPassword,
                  database: DATABASE
				});
        var WEBAPP = this;
        this.conn.connect(function(err) {
              if (err) {
                  WEBAPP.setMessage("enable to connect to the database :"+ WEBAPP.getDatabase(), false);
                  return false;
              } else {
                  WEBAPP.setMessage(" Connection established to"+ WEBAPP.getDatabase() +" successfully !!", true);
                  FUN();
                  return false;
              }
        });
    } 
}
class admin {
    constructor(tablename,primaryKey,conns){
        var record = "no record found";
        this.lastId = 0;
        this.table = tablename;
        this.conn = conns;
        this.primary_key = primaryKey;
        this.sql = "SELECT * FROM `"+this.table+"` WHERE 1";
        let mess = "no error found";
        let err = false;
        this.PIP = false;
        this.message = ()=>{
            return mess;
        }
        this.set_message = (MES)=>{
            mess = MES;
        }
        this.set_records = (RESU) =>{
            record = RESU;
            this.PIP = new PIP_Array(RESU);
        }
        this.records = () =>{
            return record;
        }
        this.JS = (INDEX) =>{
            return record[INDEX];
        }
        this.setEr = (VAL) => {
            err = VAL;
        }

    }
    SELECT_DATAS( FUN ){
        var con = mysql.createConnection({
              host: this.conn.hostName,
              user: this.conn.userName,
              password: this.conn.password,
              database: this.conn.database
            });
        var TABLE = this;
        con.query(TABLE.sql, function (err, result, fields){
            if (err) throw err;
            con.end( function(errf){
              if(errf) throw errf;
              TABLE.set_records(result);
              //console.log(FUN)
              FUN();
            })
            return result;
        });
    }
    _gets_(FUN, fields = [], values = [] , conds = [] , order = "", start = -1 , _lenght = 0 ){
        this.sql = "SELECT * FROM `"+this.table+"`";
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
                      console.log(this.message()); 
                   }
                } else {
                    this.set_message("Error : number of fields and number of values must be equal. fields: " + fields.length + " values: "+values.length);
                      console.log(this.message());
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
        if((!Array.isArray(fields))&&(!Array.isArray(fields))) return this.add(FUN,[fields],[values],datefileds);
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
            var con = mysql.createConnection({
                  host: this.conn.hostName,
                  user: this.conn.userName,
                  password: this.conn.password,
                  database: this.conn.database
                });
            var TABLE = this;
            con.query(TABLE.sql, function (err, result) {
                if(err){
                   throw err; 
                   con.end( function(errf){
                      if(errf) throw err;
                      TABLE.set_message(TABLE.table + " Records failed to be added");
                   })
                   
                } else {
                  TABLE.lastId = this._results[0].insertId;
                  con.end( function(errf){
                    if(errf) throw errf;
                    TABLE.set_message(TABLE.table + " record added successfull");
                    
                    FUN(TABLE);
                  })
                    
                }
              });
        } else {
            this.setEr(false);
            this.set_message(" Number of fields must equal to the number of values ! ");
        }
    }
    delete(FUN,values,fields = ""){
        var fields_ = this.primary_key;
        if(!(fields==""))
            fields_ = fields;
        this.sql = "DELETE FROM `"+this.table+"` WHERE `"+this.table+"`.`"+fields_+"` = "+values;
        var con = mysql.createConnection({
                  host: this.conn.hostName,
                  user: this.conn.userName,
                  password: this.conn.password,
                  database: this.conn.database
         });
        var TABLE = this;
        con.query(TABLE.sql, function (err, result) {
            if (err) {
               throw err; 
               con.end( function(errf){
                  if(errf) throw errf;
                    TABLE.set_message(TABLE.table + " Records failed to be removed");
               })
               
            } else {
              TABLE.lastId = this._results[0].insertId;
              con.end( function(errf){
                  if(errf) throw errf;
                    TABLE.set_message(TABLE.table + " record removed successfull");
                  FUN(TABLE); 
              })
               
            }
          });
        
    }
    edit(FUN,values,fields,id){
        this.sql = "UPDATE `"+this.table+"`  SET "
        
        for(var cc = 0; cc<values.length; cc++){
            if(cc>0)
              this.sql += ",";
              this.sql += "`"+fields[cc]+"` = `"+values[cc]+"`"
            
        }
        
        this.sql += "WHERE `"+this.table+"`.`"+this.primary_key+"` = "+id;
        var con = mysql.createConnection({
                  host: this.conn.hostName,
                  user: this.conn.userName,
                  password: this.conn.password,
                  database: this.conn.database
                });
        var TABLE = this;
        con.query(TABLE.sql, function (err, result) {
            if (err) {
               throw err;
               con.end( function(errf){
                  if(errf) throw errf;
                  TABLE.set_message(TABLE.table + " Records failed to be updated");
               })
               
            } else {
              con.end( function(errf){
                 if(errf) throw errf;
                 TABLE.set_message(TABLE.table + " record updated successfull");
                 TABLE.lastId = this._results[0].insertId;
                 FUN(TABLE);
              })
                
            }
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
}
class PipMysql{
     constructor(userName,password,dataBase){
     	this.conn = mysql.createConnection({
				  host: "localhost",
				  user: userName,
				  password: password,
                  database: dataBase
				});
         this.conn.connect(function(err) {
              if (err) throw err;
              console.log("Connected success!");
            });
         }
     NewDataBase(name){
         
         this.conn.query("CREATE DATABASE  IF NOT EXISTS "+name, function (err, result) {
            if (err) throw err;
            console.log("Database created");
          });   
     }
     connections() {
         return this.conn;
     }
}
class Table {
     constructor(name,conn,primarykey){
     	    this.conn = conn;
            this.primary = primarykey;
            this.name = name;
          }
     getAllDatas(table,limit,condition){
         
     }
     saveDatas(fields,datas,timestamp){
          if (!(fields.length==datas.length)) console.log('please in put equal values '+fields.length+'  == '+datas.length)
          else{
              var allfields = "( ";
              var allvalues = "( ";
              var times = typeof timestamp;
              var prim = typeof this.primary;
              if(!(prim=="boolean")){
                   allfields = allfields + this.primary+",";
                   allvalues = allvalues + 'NULL,'
                      }
             for(var xx=0; xx<fields.length; xx++){
               allfields = allfields +  fields[xx];
               allvalues = allvalues + "'" + datas[xx]+ "'" ;
                 if(xx==(fields.length-1)){
                     if(!(times=="boolean")){
                          allfields = allfields + "," + timestamp;
                          allvalues = allvalues + ',CURRENT_TIMESTAMP'
                      }
                     allfields = allfields + " )";
                     allvalues = allvalues + " )";
                 } else 
                     {
                     allfields = allfields + ","; 
                     allvalues = allvalues + ",";
                 }
                     
             }
              
          var sql = "INSERT INTO " + this.name + " " + allfields + " VALUES " + allvalues;
          this.conn.query(sql, function (err, result) {
            if (err) throw err;
            console.log("1 record inserted");
           });    
          }
     }
     saveDatasCurrentDate(table,fields,datas,datefields){
         
     }
     registerDatas(table,fields,datas,unique){
         
     }
     registerDatasCurrentDate(table,fields,datas,unique,datefields){
         
     }
}
class PIP_Array{
    constructor(DATAS){
        this.AV = DATAS;
        this.RV = DATAS.reverse();
        
        this.size = DATAS.length;
    }
    _gets_(ind,val,type="REMOVE"){
       var DATS = [];
        for(var t =0 ; t<this.size;t++){
            if(type=="REMOVE"){
              //console.log("foundss:")
              //console.log(eval("this.AV["+t+"]."+ind));
               if(!(eval("this.AV["+t+"]."+ind)==val)){
                    DATS.push(this.AV[t]);
                    //console.log("foundss:")
                } else {
                  //console.log(eval("this.AV["+t+"]."+ind));
                }
            } else {
               if(eval("this.AV["+t+"]."+ind)==val){
                    DATS.push(this.AV[t]);
                } 
            }
            
        }
       return new PIP_Array(DATS);
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
        var ARR = this.getsD(ind2);
        if(theArray2.size>0){
             console.log("the size:"+theArray2.size)
             var all = theArray2.getsD(ind1);

             for(var ii=0;ii<all.size;ii++){
                   ARR = ARR._gets_(ind2,all.AV[ii][ind1],type);
             }
             return new PIP_Array(ARR.AV);
        } else return this.empty();
         
    }

    empty(){
        return new PIP_Array(new Array());
    }
    JS(index){
        return this.AV[index];
    }
}
exports.database = (database_name)=>{
    return new PipMysql('root','',database_name);
}
exports.table = (conn,primarykey,datefield)=>{
    return new Table(conn,primarykey,datefield);
}
exports.WebApp = (databaseName)=>{
    return new WebApp(databaseName);
}
exports.admin = (tablename,primaryKey,conns)=>{
    return new admin(tablename,primaryKey,conns);
}