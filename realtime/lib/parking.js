var database = require('./pipMysql.js'); 
var park_2 = database.WebApp("park_2");

var devices_taps = database.admin("devices_taps","devices_taps_id",park_2.connection());
var devices_logs = database.admin("devices_logs","devices_logs_id",park_2.connection());
var devices = database.admin("devices","devices_id",park_2.connection());
var bulding_devices = database.admin("bulding_devices","bulding_devices_id",park_2.connection());
var devices_connectivity = database.admin("devices_connectivity","devices_connectivity_id",park_2.connection());
var devices_logs = database.admin("devices_logs","devices_logs_id",park_2.connection());
var cards = database.admin("cards","cards_id",park_2.connection());
var cards_subscribers = database.admin("cards_subscribers","cards_subscribers_id",park_2.connection());
var payments = database.admin("payments","payments_id",park_2.connection());
var payment_users = database.admin("payment_users","payments_id",park_2.connection());
var payments_archives = database.admin("payments_archives","payments_archives_id",park_2.connection());
var bulding_gate = database.admin("bulding_gate","bulding_gate_id", park_2.connection());

var gateTypes = ["Entrance","Exit","blocked","Entrance and Exit"];

var device_taps = function(bid, FUN){
    park_2._connect( function(){
        devices_taps._gets_( function(){
            FUN(devices_taps);
        },["bid"],[bid]);
    });
}

class gate {
    constructor(io){
        var status = "closed";
        var cardReader = {
            device_id : 0,
            serial: '00000000',
            ip_v4:"",
            ip_v6:""
        };
        var plateReader = {
            device_id : 0,
            serial: '00000000',
            ip_v4:"",
            ip_v6:""
        };
        var online = false;
        var bid = 0;
        this.type = 3;
        this.io = io;
        this.NFC = (device_id, serial, ip_v4, ip_v6) =>{
            cardReader.device_id = device_id;
            cardReader.serial = serial;
            cardReader.ip_v4 = ip_v4;
            cardReader.ip_v6 = ip_v6;
        }
        this.camera = (device_id, serial, ipV4, ipV6) =>{
            plateReader.device_id = device_id;
            plateReader.serial = serial;
            plateReader.ip_v4 = ipV4;
            plateReader.ip_v6 = ipV6;
        }
        this.Online = (status = true) =>{
            online = status;
        }
        this.building = (BID) => {
            bid = BID;
        }
        this.details = () =>{ 
            return {
                building : bid,
                cardReader : cardReader,
                plateReader : plateReader,
                connected : online,
                type: this.type
            };
        }
    }
    // a method to open a gete if all is 0 the door of this device will be opened
    open(all = 0){
        if(all==0){
          this.io.sockets.emit('open_gate',this.details().cardReader.device_id);
          console.log("openning gate: "+ this.details().cardReader.device_id +" ...");
        } else this.io.sockets.emit('open_gate',"*");
    }
    // a method to close a gete if all is 0 the door of this device will be closed
    close(all=0){
        if(all==0){
          this.io.sockets.emit('close_gate',this.details().cardReader.device_id);
          console.log("closing gate: "+ this.details().cardReader.device_id +" ...");
        } else this.io.sockets.emit('close_gate',"*");
    }
    
    block(all=0){
        if(all==0){
          this.io.sockets.emit('block_gate',this.details().cardReader.device_id);
        } else this.io.sockets.emit('block_gate',"blocking");
    }
    // the function to initialize all datas of a gate
    init(DATAS){
        
        var device_id = 0;
        var serial = "";
        
        if(DATAS.device_id)
            device_id = DATAS.device_id;
        
        if(DATAS.serial)
            serial = DATAS.serial;
        
        var ip_v4 = "";
        if(DATAS.ip_v4)
            serial = DATAS.ip_v4;
        
        var ip_v6 = "";
        if(DATAS.ip_v6)
            serial = DATAS.ip_v6;
        
        
        
        this.NFC(DATAS.device_id, DATAS.serial, DATAS.ip_v4, DATAS.ip_v6);
        var GATE = this;
            park_2._connect( function(){
                devices._gets_(function(){
                    if(devices.records().length){
                      GATE.io.sockets.emit('online_devices',devices.JS(0).devices_id);
                      GATE.Online();
                      GATE.NFC(devices.JS(0).devices_id,DATAS.serial,DATAS.ip_v4,DATAS.ip_v6);
                      
                      bulding_devices._gets_( function(){
                          if(bulding_devices.records().length){
                              GATE.building(bulding_devices.JS(0).bid);
                              var feedBack = {
                                contents : "the device with (" + GATE.details().cardReader.serial + ") serial number is successfully connected, with local ip adress: " + GATE.details().cardReader.ip_v4,
                                status:"0"
                              }
                              GATE.io.sockets.emit("notify",feedBack);
                              
                               if(devices.records().length){
                                devices_logs.add( function(new_device_log){
                                    console.log(new_device_log.message());
                                    GATE.io.sockets.emit('devices_logs',devices.JS(0).devices_id);
                                    devices_connectivity.add( function(new_conn){
                                        console.log(new_conn.message());
                                        GATE.gateType(  function(NEW_GATE){
                                            DATAS.success(NEW_GATE);
                                            
                                        })
                                    },["devices_id","bid","ip_address"],[devices.JS(0).devices_id,bulding_devices.JS(0).bid,GATE.details().cardReader.ip_v4],"time");
                                    
                                },["devices_id","contents","status"],[devices.JS(0).devices_id,feedBack.contents,feedBack.status],"time");
                              }

                                


                                GATE.io.sockets.emit('unregisred_devices',feedBack);

                                var ip_address_found = DATAS.ip_v4+"<"+devices.JS(0).devices_id+"<"+bulding_devices.JS(0).bid;

                                GATE.io.sockets.emit('ip_address_found',ip_address_found);

                                // getting all registered cards from this building
                                cards._gets_( function(){
                                    var list_of_cards = "";
                                    for(var i = 0; i < cards.records().length; i++){
                                        list_of_cards = list_of_cards +"<";
                                    }
                                    GATE.io.sockets.emit("allowed_cards",list_of_cards);
                                },["bid"],[bulding_devices.JS(0).bid]);
                                
                                
                              
                          } 
                          else{
                              var feedBack = {
                                contents : "the device with (" + GATE.details().cardReader.serial + ") serial number is not assigned to any building, this device will not work property and the server will allways reject any request from it, like card reading and gate control.",
                                status:"2"
                                }
                                GATE.io.sockets.emit("notify",feedBack);

                                GATE.io.sockets.emit('unregisred_devices',feedBack);
                                devices_logs.add( function(new_device_log){
                                    //console.log(new_device_log);
                                    GATE.io.sockets.emit('devices_logs',devices.JS(0).devices_id);
                                    console.log(feedBack);
                                    DATAS.success(GATE);
                                },["devices_id","contents","status"],[devices.JS(0).devices_id,feedBack.contents,feedBack.status],"time");
                               
                                
                          }
                      },["devices_id"],[devices.JS(0).devices_id]);
                    } 
                    else {
                       var feedBack = {
                        contents:" unkown device with ("+ GATE.details().cardReader.serial +") serial number is trying to connect to the server, the device was rejected and will not be allowed to take any action",
                        status:"3"
                        }
                        GATE.io.sockets.emit("notify",feedBack);
                        
                        devices_logs.add( function(new_device_log){
                            //console.log(new_device_log);
                            GATE.io.sockets.emit('devices_logs',0);
                            GATE.io.sockets.emit('unregisred_devices',feedBack);
                            console.log(feedBack);
                            DATAS.success(GATE);
                        },["devices_id","contents","status"],[0,feedBack.contents,feedBack.status],"time");
                        
                        
                    }
                },["serial"],[GATE.details().cardReader.serial]);
            });
    }
    // a function to set and to get the gate type.
    gateType(FUN,TYPE=3){
        this.type = TYPE;
        var GATE = this;
        park_2._connect( function(){
            bulding_gate._gets_( function(){
                if(bulding_gate.records().length){
                    GATE.type = bulding_gate.JS(0).bulding_gate_type;
                }
                   console.log(GATE.type);
                   FUN(GATE);
            },["card_reader"],[GATE.details().cardReader.device_id]);
        })
    }
    // a function to be used in order to send previous datas to the device if device is new connected
    send(datas){
      var GATE = this;
      if(datas.records().length>0){
          cards._gets_( function(){
              // payments._gets_( function(){
              //     var smartTaps = datas.PIP.getsM(payments.PIP._gets_("status","2"),"taps","devices_taps_id");
              //     console.log(payments.PIP._gets_("status","2").size);
              // },["bid"],[datas.JS(0).bid])
               var compacts = "("+GATE.details().cardReader.device_id+")";
               for(var t =0 ; t<cards.records().length;t++){
                   compacts = compacts+"<"+cards.JS(t).cards_number+":0>";
               }

               for(var t = datas.records().length-1; t>=0; t--){
                      compacts = compacts+"<"+datas.JS(t).cards_number+":"+datas.JS(t).direction+">";
               }
               //console.log(compacts);
               GATE.io.sockets.emit("previous_data",compacts);

          },["bid"],[datas.JS(0).bid]);

      }
       //
    }
    // a function to be used in order to receive activities done while the server was at sleep
    receive(){
        //this.io.sockets.emit("give_me_some_updates",this.details().cardReader.device_id);
    }
    // a function to be called while entering
    enter(card,msg){
      var compound = msg[0].card_number+"<"+msg[0].money_to_be_payed+"<" + msg[0].time_elapsed+"<"+msg[0].building_id+"<"+msg[0].device_id+"<"+msg[0].timePosted+"<"+1;
      if(card.cardId()>0){
      if(card.building()==this.details().building){  
        if(card.cards_subscribers.user_id>0){
            if((this.type==0)||(this.type==3)){
                var now = new Date();
                if((card.cards_subscribers.end_date - now)<0){
                  
                  var feedBack = {
                    contents : "The device with (" + this.details().cardReader.serial + ") serial number is being used as the entrance with expired card, note that all expired cards will not be allowed to be used",
                    status:"2"
                  }
                  
                  this.io.sockets.emit("notify",feedBack);
                    var GATE = this;
                    devices_logs.add( function(new_device_logs){
                        console.log(new_device_logs.message());
                        GATE.io.sockets.emit("devices_logs",new_device_logs.lastId);
                    },["devices_id","contents","status"],[this.details().cardReader.device_id,feedBack.contents,feedBack.status],"time");
                } else {
                    var GATE = this;
                   devices_taps.add( function(new_devices_taps){
                       console.log(new_devices_taps.message());
                       var feedBack = {
                            contents : "The device with (" + GATE.details().cardReader.serial + ") serial number has been used (with subscription card) as entrance successfully",
                            status:"0"
                        }
                       GATE.io.sockets.emit("notify",feedBack);
                       devices_logs.add( function(new_devices_logs){
                           console.log(new_devices_logs.message());
                           GATE.io.sockets.emit("devices_logs",new_devices_logs.lastId);
                           GATE.io.sockets.emit("add_a_car",compound);
                           var newCompound = msg[0].card_number+"<"+msg[0].device_id;
                           GATE.io.sockets.emit("car_in",newCompound);
                           GATE.open();
                       },["devices_id","contents","status"],[GATE.details().cardReader.device_id,feedBack.contents,feedBack.status],"time")
                   },["devices_id","bid","direction","cards_number"],[GATE.details().cardReader.device_id,GATE.details().building,1,card.cardnumber()],"time"); 
                }
            }
            else {
                var feedBack = {
                    contents : "The device with (" + this.details().cardReader.serial + ") serial number is not for entrance, the device will not be accepted as entrance, may be is for exit only or the gate is blocked!",
                    status:"1"
                    
                }
                this.io.sockets.emit("notify",feedBack);
                var GATE = this;
                devices_logs.add( function(devices_logs){
                    console.log(devices_logs.message());
                    GATE.io.sockets.emit("devices_logs",devices_logs.lastId);
                },["devices_id","contents","status"],[this.details().cardReader.device_id,feedBack.contents,feedBack.status],"time");
            }
        } 
        else {

            if((this.type==0)||(this.type==3)){
               var GATE = this;
               devices_taps.add( function(new_devices_taps){
                   console.log(new_devices_taps.message());
                   var feedBack = {
                        contents : "The device with (" + GATE.details().cardReader.serial + ") serial number has been used as the entrance successfully",
                        status:"0"
                    }
                   GATE.io.sockets.emit("notify",feedBack);
                   devices_logs.add( function(new_devices_logs){
                       console.log(new_devices_logs.message());
                       GATE.io.sockets.emit("devices_logs",new_devices_logs.lastId);
                       GATE.io.sockets.emit("add_a_car",compound);
                       var newCompound = msg[0].card_number+"<"+msg[0].device_id;
                       GATE.io.sockets.emit("car_in",newCompound);
                       GATE.open();
                   },["devices_id","contents","status"],[GATE.details().cardReader.device_id,feedBack.contents,feedBack.status],"time")
               },["devices_id","bid","direction","cards_number"],[GATE.details().cardReader.device_id,GATE.details().building,1,card.cardnumber()],"time"); 
            } 
            else {
                var feedBack = {
                    contents : "The device with (" + this.details().cardReader.serial + ") serial number is not for entrance, the device will not be accepted as entrance, may be is for exit only or the gate is blocked!",
                    status:"1"
                }
                this.io.sockets.emit("notify",feedBack);
                var GATE = this;
                devices_logs.add( function(new_devices_logs){
                    console.log(new_devices_logs.message());
                    GATE.io.sockets.emit("devices_logs",new_devices_logs.lastId);
                },["devices_id","contents","status"],[this.details().cardReader.device_id,feedBack.contents,feedBack.status],"time");
            }
        }
      } 
      else {
          var feedBack = {
                contents : "The device with (" + this.details().cardReader.serial + ") is being used for entrance with cards registered with different building!",
                status:"2"
          }
          this.io.sockets.emit("notify",feedBack);
          
            var GATE = this;
            devices_logs.add( function(new_devices_logs){
                console.log(new_devices_logs.message());
                GATE.io.sockets.emit("devices_logs",new_devices_logs.lastId);
            },["devices_id","contents","status"],[this.details().cardReader.device_id,feedBack.contents,feedBack.status],"time");
          
          
      }
      } 
      else {
         var feedBack = {
                contents : "The device with (" + this.details().cardReader.serial + ") is being used as entrance with unregisted card:("+card.cardnumber()+") unregistered cards will allways be rejected by the sytem server!",
                status:"2"
          }
         this.io.sockets.emit("notify",feedBack);
         var GATE = this;
            devices_logs.add( function(new_devices_logs){
                console.log(new_devices_logs.message());
                GATE.io.sockets.emit("devices_logs",new_devices_logs.lastId);
            },["devices_id","contents","status"],[this.details().cardReader.device_id,feedBack.contents,feedBack.status],"time");
      }
    }
    // a function to be called while exiting
    exit(card,msg){
        var compound = msg[0].card_number+"<"+msg[0].money_to_be_payed+"<" + msg[0].time_elapsed+"<"+msg[0].building_id+"<"+msg[0].device_id+"<"+msg[0].timePosted+"<"+0;
        var GATE = this;
        if(card.cardId()>0){
         if(card.building()==this.details().building){
          if((this.type==1)||(this.type==3)){
           if(card.cards_subscribers.user_id>0){
               devices_taps.add( function(new_devices_taps){
                   console.log(new_devices_taps.message());
                   var feedBack = {
                        contents : "The device with (" + GATE.details().cardReader.serial + ") is used as an exit with subscription card ("+card.cardnumber()+") success !",
                        status:"0"
                   }
                   
                   GATE.io.sockets.emit("notify",feedBack);
                   
                   devices_logs.add( function(new_devices_logs){
                       console.log(new_devices_logs.message());
                       payment_users.add( function(new_payment_users){
                          GATE.open();
                          //GATE.io.sockets.emit("add_a_car",compound);
                          var newCompound = msg[0].card_number+"<"+msg[0].building_id;
                          GATE.io.sockets.emit('car_out',newCompound);
                          GATE.io.sockets.emit("devices_logs",new_devices_logs.lastId);
                       },["amount","devices_id","bid","card_number","tap"],[msg[0].money_to_be_payed,msg[0].device_id,msg[0].building_id,msg[0].card_number,new_devices_taps.lastId],"time");
                   },["devices_id","contents","status"],[GATE.details().cardReader.device_id, feedBack.contents, feedBack.status],"time");
               },["devices_id","bid","direction","cards_number"],[GATE.details().cardReader.device_id,GATE.details().building,0,card.cardnumber()],"time")

              
           } 
           else if(card.cardType()==3){
               var GATE = this;
               devices_taps.add( function(new_devices_taps){
                   console.log(new_devices_taps.message());
                   var feedBack = {
                        contents : "The device with (" + GATE.details().cardReader.serial + ") is used as an exit with a super card("+card.cardnumber()+") success !",
                        status:"0"
                   }
                   GATE.io.sockets.emit("notify",feedBack);
                   
                   devices_logs.add( function(new_devices_logs){
                       console.log(new_devices_logs.message())
                       var newCompound = msg[0].card_number+"<"+msg[0].building_id;
                       GATE.io.sockets.emit('car_out',newCompound);
                       GATE.io.sockets.emit("devices_logs",new_devices_logs.lastId);
                       GATE.open();
                       //GATE.io.sockets.emit("add_a_car",compound);
                   },["devices_id","contents","status"],[GATE.details().cardReader.device_id, feedBack.contents, feedBack.status],"time");
               },["devices_id","bid","direction","cards_number"],[this.details().cardReader.device_id,this.details().building,0,card.cardnumber()],"time")
           } 
           else {
               GATE = this;
               switch(card.lastTap.direction){
                   case 0:{
                       switch(card.lastPayment.status){
                           case 0:{
                               devices_taps.delete( function(new_device_taps){
                                   console.log(new_device_taps.message());
                                   payments.delete( function(new_payments){
                                       console.log(new_payments.message());
                                       new_device_taps.add( function(new_device_taps){
                                          console.log(new_device_taps.message());
                                          card.lastTap.devices_taps_id = new_device_taps.lastId;
                                           new_payments.add( function(new_payments){
                                               console.log(new_payments.message());
                                               let new_pay_sends = {
                                                   "pay_id":new_payments.lastId,
                                                   "device_id":msg[0].device_id,
                                                   "status":"0"
                                               }
                                               GATE.io.sockets.emit("new_payment",new_pay_sends);
                                               var feedBack = {
                                                    contents : "The device with (" + GATE.details().cardReader.serial + ") is used as an exit with a unpayed card("+card.cardnumber()+"): note that the unpayed cards wont work until clearence are found!, the new invoice was generated",
                                                    status:"3"
                                               }
                                               GATE.io.sockets.emit("notify",feedBack);
                                               
                                           },["amount","devices_id","bid","card_number","tap"],[msg[0].money_to_be_payed,msg[0].device_id,msg[0].building_id,msg[0].card_number,card.lastTap.devices_taps_id],"time")
                                       },["devices_id","bid","direction","cards_number"],[GATE.details().cardReader.device_id,GATE.details().building,0,card.cardnumber()],"time");
                                   },card.lastPayment.payments_id);
                               },card.lastTap.devices_taps_id);
                               break;
                           }
                           case 1:{
                               devices_taps.delete( function(new_device_taps){
                                   console.log(new_device_taps.message());
                                   payments.delete( function(new_payments){
                                       console.log(new_payments.message());
                                       new_device_taps.add( function(new_device_taps){
                                          console.log(new_device_taps.message());
                                          card.lastTap.devices_taps_id = new_device_taps.lastId;
                                           new_payments.add( function(new_payments){
                                               console.log(new_payments.message());
                                               let new_pay_sends = {
                                                   "pay_id":new_payments.lastId,
                                                   "device_id":msg[0].device_id,
                                                   "status":"0"
                                               }
                                               GATE.io.sockets.emit("new_payment",new_pay_sends);
                                               var feedBack = {
                                                    contents : "The device with (" + GATE.details().cardReader.serial + ") is used as an exit ("+card.cardnumber()+"), but it seems the card has payed but the gate is not open !, the new invoice was generated",
                                                    status:"1"
                                               }
                                               GATE.io.sockets.emit("notify",feedBack);

                                               devices_logs.add( function(new_devices_logs){
                                                   console.log(new_devices_logs.message())
                                                   GATE.io.sockets.emit("devices_logs",new_devices_logs.lastId);
                                               },["devices_id","contents","status"],[GATE.details().cardReader.device_id, feedBack.contents, feedBack.status],"time");
                                               
                                           },["amount","devices_id","bid","card_number","tap"],[msg[0].money_to_be_payed,msg[0].device_id,msg[0].building_id,msg[0].card_number,card.lastTap.devices_taps_id],"time")
                                       },["devices_id","bid","direction","cards_number"],[GATE.details().cardReader.device_id,GATE.details().building,0,card.cardnumber()],"time");
                                   },card.lastPayment.payments_id);
                               },card.lastTap.devices_taps_id);
                               break;
                           }
                           case 2:{
                               var feedBack = {
                                    contents : "The device with (" + GATE.details().cardReader.serial + ") is used as an exit ("+card.cardnumber()+"), but it seems the card has payed but the gate is opened!, this card has an internal system error and its records must me cleared to prevents more problems",
                                    status:"3"
                               }
                               devices_logs.add( function(new_devices_logs){
                                   console.log(new_devices_logs.message())
                                   GATE.io.sockets.emit("devices_logs",new_devices_logs.lastId);
                               },["devices_id","contents","status"],[GATE.details().cardReader.device_id, feedBack.contents, feedBack.status],"time");
                               break;
                           }
                           default:{
                               var feedBack = {
                                    contents : "The device with (" + GATE.details().cardReader.serial + ") is used as an exit ("+card.cardnumber()+"), has unkown system error!",
                                    status:"2"
                               }
                               devices_logs.add( function(new_devices_logs){
                                   console.log(new_devices_logs.message())
                                   GATE.io.sockets.emit("devices_logs",new_devices_logs.lastId);
                               },["devices_id","contents","status"],[GATE.details().cardReader.device_id, feedBack.contents, feedBack.status],"time");
                               break;
                           }
                       }
                       break;
                   }
                   case 1:{
                       devices_taps.add( function(new_devices_tap){
                           console.log(new_devices_tap.message());
                           payments.add( function(new_payments){
                               console.log(new_payments.message())
                               var feedBack = {
                                    contents : "The device with (" + GATE.details().cardReader.serial + ") serial number has been used as exit with card("+card.cardnumber()+") success!",
                                    status:"0"
                               }
                               GATE.io.sockets.emit("notify",feedBack);
                               
                               devices_logs.add( function(new_devices_logs){
                                   console.log(new_devices_logs.message())
                                   GATE.io.sockets.emit("devices_logs",new_devices_logs.lastId);
                                   let new_pay_sends = {
                                                   "pay_id":new_payments.lastId,
                                                   "device_id":msg[0].device_id,
                                                   "status":"0"
                                               }
                                   GATE.io.sockets.emit("new_payment",new_pay_sends);
                               },["devices_id","contents","status"],[GATE.details().cardReader.device_id, feedBack.contents, feedBack.status],"time");
                               
                           },["amount","devices_id","bid","card_number","tap"],[msg[0].money_to_be_payed,msg[0].device_id,msg[0].building_id,msg[0].card_number,new_devices_tap.lastId],"time");
                       },["devices_id","bid","direction","cards_number"],[this.details().cardReader.device_id,this.details().building,0,card.cardnumber()],"time")
                       break;
                   }
                   case -1:{
                       var feedBack = {
                            contents : "The device with (" + this.details().cardReader.serial + ") serial number is being used as exit ( card: "+card.cardnumber()+") but there was no entrance found, the wrong card was used or there is an internal system error",
                            status:"2"
                        }
                       GATE.io.sockets.emit("notify",feedBack);
                        devices_logs.add( function(new_devices_logs){
                           console.log(new_devices_logs.message())
                           GATE.io.sockets.emit("devices_logs",new_devices_logs.lastId);
                       },["devices_id","contents","status"],[GATE.details().cardReader.device_id, feedBack.contents, feedBack.status],"time");
                       break;
                   }
                   default:{
                       var feedBack = {
                            contents : "The device with (" + this.details().cardReader.serial + ") serial number is being used as exit ( card: "+card.cardnumber()+") with internal system error",
                            status:"2"
                        }
                        GATE.io.sockets.emit("notify",feedBack);
                        devices_logs.add( function(new_devices_logs){
                           console.log(new_devices_logs.message())
                           GATE.io.sockets.emit("devices_logs",new_devices_logs.lastId);
                       },["devices_id","contents","status"],[GATE.details().cardReader.device_id, feedBack.contents, feedBack.status],"time");
                       break
                   }
               }
           }
          } 
          else {
              var feedBack = {
                    contents : "The device with (" + this.details().cardReader.serial + ") serial number is not for exit, the device will not be accepted as exit, may be is for entrance only or the gate is blocked!",
                    status:"1"
                }
                this.io.sockets.emit("notify",feedBack);
                var GATE = this;
                devices_logs.add( function(new_device_log){
                    console.log(new_device_log.message());
                    GATE.io.sockets.emit('devices_logs',GATE.details().cardReader.device_id);
                },["devices_id","contents","status"],[this.details().cardReader.device_id,feedBack.contents,feedBack.status],"time");
          }
         } 
         else {
            var feedBack = {
                contents : "The device with (" + this.details().cardReader.serial + ") is being used for exit with cards registered for different building!",
                status:"2"
                }
               
               this.io.sockets.emit("notify",feedBack);

                var GATE = this;
                devices_logs.add( function(new_device_log){
                    console.log(new_device_log.message());
                    GATE.io.sockets.emit('devices_logs',GATE.details().cardReader.device_id);
                },["devices_id","contents","status"],[this.details().cardReader.device_id,feedBack.contents,feedBack.status],"time"); 
         }
        } 
        else {
         var feedBack = {
                contents : "The device with (" + this.details().cardReader.serial + ") is being used as exit with unregisted card: unregistered cards will allways be rejected by the sytem server!",
                status:"2"
         }
         this.io.sockets.emit("notify",feedBack);
         var GATE = this;
            devices_logs.add( function(new_device_log){
                console.log(new_device_log.message());
                GATE.io.sockets.emit('devices_logs',GATE.details().cardReader.device_id);
            },["devices_id","contents","status"],[this.details().cardReader.device_id,feedBack.contents,feedBack.status],"time");
        }
    }
}
class card {
    constructor(CARS_NUMBS = "XXX"){
        var number = CARS_NUMBS;
        var id = 0;
        var bid = 0;
        var type =  0;
        this.cards_subscribers = {
            user_id:0,
            start_date: new Date(),
            end_date: new Date()
        }
        this.lastTap = {
            direction:-1,
            time:new Date(),
            devices_id:0,
            devices_taps_id:0
        }
        
        this.lastPayment = {
            tap:-1,
            status:-1,
            devices_id:-1,
            payments_id:-1,
            time:new Date()
        }
        
        this.card_type = (Type = 0)=>{
            type = Type;
        }
        
        this.cardType = ()=>{
            return type;
        }
        
        this.card_id = (cid = id) =>{
            id = cid;
        }
        this.buildin = (Bid) =>{
            bid = Bid;
        }
        this.cardNumber = ( cnum = number ) =>{
            number = cnum;
            return number
        }
        
        this.building = ()=>{
            return bid;
        }
        this.cardnumber = ()=>{
            return number;
        }
        
        this.cardId = ()=>{
            return id;
        }
    }
    
    init(FUN){
       var CARD = this;
       park_2._connect( function(){
           cards.search( function(){
               if(cards.records().length){
                   CARD.card_id(cards.JS(0).cards_id);
                   CARD.buildin(cards.JS(0).bid);
                   CARD.card_type(cards.JS(0).type);
                   cards_subscribers._gets_( function(){
                      if(cards_subscribers.records().length){
                          CARD.cards_subscribers.user_id = cards_subscribers.JS(0).user_id;
                          CARD.cards_subscribers.start_date = cards_subscribers.JS(0).start_date;
                          CARD.cards_subscribers.end_date = cards_subscribers.JS(0).end_date;
                      } else if(cards.JS(0).type==3) {
                          console.log("this is card has a permanent access");
                      } else {
                          console.log("this is a tap and pay card");
                      }
                       
                      devices_taps.search( function(){
                          if(devices_taps.records().length){
                              //console.log(devices_taps.records());
                              //console.log(devices_taps.sql);
                              let last = devices_taps.records().length-1;
                              CARD.lastTap.devices_id = devices_taps.JS(last).devices_id;
                              CARD.lastTap.time = devices_taps.JS(last).time;
                              CARD.lastTap.direction = devices_taps.JS(last).direction;
                              CARD.lastTap.devices_taps_id = devices_taps.JS(last).devices_taps_id;
                              payments.search( function(){
                                  if(payments.records().length){
                                      let lastP = payments.records().length-1;
                                      //console.log(payments.records());
                                      //console.log(payments.sql);
                                      CARD.lastPayment.devices_id = payments.JS(lastP).devices_id;
                                      CARD.lastPayment.status = payments.JS(lastP).status;
                                      CARD.lastPayment.tap = payments.JS(lastP).tap;
                                      CARD.lastPayment.time = payments.JS(lastP).time;
                                      CARD.lastPayment.payments_id = payments.JS(lastP).payments_id
                                      FUN(CARD);
                                  } else {
                                      console.log("A card with: "+CARD.cardNumber() +" has no existing payments");
                                      FUN(CARD);
                                  }
                              },["card_number"],[CARD.cardNumber()],[],[]);
                          } else {
                             console.log("A card with: "+CARD.cardNumber() +" has no existing taps");
                             FUN(CARD); 
                          }
                          
                      },["cards_number"],[CARD.cardNumber()],[],[]);
                      
                   },["cards_id"],[CARD.cardId()]);
               } else {
                   console.log("A card with: "+CARD.cardNumber() +" is not found in the system in the system");
                   FUN(CARD);
               }
           },["cards_number"],[CARD.cardNumber()],[],[]);
       }); 
    }
    // a function to block a card from being used.
    block(){
        
    }
}
class slots{
    constructor(){
        
    }
}
class building{
    constructor(cards = [], gates = [], slots = []){
        var cards = cards;
        var gates = gates;
        var slots = slots;
        this.card = (num) =>{
            return cards[num];
        }
        this.gate = (num) =>{
            return gates[gat];
        }
        this.slot = (num) =>{
            return slots[num];
        }
    }
}
class plateReader{
    
}

class payment{
    constructor(){
        this.payments = payments;
        this.payment_users = payment_users;
        //this.payments_archives = 
    }
}


exports.gate = function(IO){
    return new gate(IO);
}

exports.card = function(NUM){
    return new card(NUM);
}

exports.taps = function(BID, FUN){
    return device_taps(BID, FUN);
}



