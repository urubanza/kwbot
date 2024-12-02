class report{
    constructor(file=""){
        var MONTHS = ["January","February","March","April","May","June","July","August","September","October","November","December"];
        var DAYS = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
        var orientation = 'portrait';
        this.name = "Random report";
        if(file.orientation)
            orientation = filename.orientation;
        if(file.name)
           this.name  = file.name;
        this.doc = new jsPDF({ putOnlyUsedFonts: true, orientation: orientation });
        this.doc.setFont("Arial","normal");
        this.doc.setFontSize(10);
        var Dats = new Date();
        var finalStr = DAYS[Dats.getDay()]+", "+OrdinaryNumber(Dats.getDate())+" "+MONTHS[Dats.getMonth()]+" "+Dats.getFullYear();
        this.doc.text(finalStr,10,64);
        this.pointer = {
            "x":0,
            "y":0
        }
        
        this.pages = 1;
        /*
           the keyTitle must be of pipArray index, title to display and width of the field in the table. 
           
           keyTitle = {
                "index":"_ids",
                "title":"example",
                "size":"30"
                "date":false
           }
        */
        this.keysTitle = new Array();
    }
    company(name = "IziWord LTD"){
        this.doc.text(name,10,40);
        return this;
    }
    web(WEBS = "www.iziworld.com"){
        this.doc.text(WEBS,10,44);
        return this;
    }
    tel(t = "+250788616464/+250728717474"){
        this.doc.text("tel : "+t,10,48);
        return this;
    }
    email(em = "info@iziworld.com"){
        this.doc.text("Email : "+em,10,52);
        return this;
    }
    buiding(name){
        this.doc.text(name,10,60);
        return this;
    }
    title(titl){
        this.doc.setFontSize(20);
        this.doc.setFont("Arial","bold");
        //this.doc.text(" PAYMENT REPORT OF AUGUST 2020",40,75);
        //var lens = ;
        if(titl.length<40)
            this.doc.text(titl,40,75);
        else this.doc.text(titl.substring(0,40),40,75);
        return this;
    }
    keys(keyTitle){
        this.keysTitle.push(keyTitle);
        return this;
    }
    datas(JS_AP){
        this.doc.rect(10,80,190,10,"F");
        this.doc.setFontSize(10);
        this.doc.setTextColor('#FFFFFF');
        this.doc.text("#",15,86);
        var additionals = 24;
        for(var ii=0;ii<this.keysTitle.length;ii++){
            this.doc.text("| "+this.keysTitle[ii].title,additionals,86);
            additionals = additionals+parseInt(this.keysTitle[ii].size);
        }
        
        this.doc.setTextColor('#000000');
        this.doc.setFont("Arial","normal");
        var MorePagePointer = 0;
        for(var x=0;x<JS_AP.data.length;x++){
            if(x<20){
                var addsx = 95+(x*10);
                this.doc.text(""+(x+1),15,addsx);
                var additionals = 24;
                for(var ii=0;ii<this.keysTitle.length;ii++){
                    var textTo = eval("JS_AP.data[x]."+this.keysTitle[ii].index);
                    if(this.keysTitle[ii].date){
                        var Dats = new Date(textTo);
                        textTo = OrdinaryNumber(Dats.getDate())+" "+MONTHS[Dats.getMonth()]+" "+Dats.getFullYear();
                    }
                    textTo = "| "+textTo    
                        this.doc.text(textTo,additionals,addsx);
                        additionals = additionals+parseInt(this.keysTitle[ii].size);
                }
             } else {
                 if(MorePagePointer==0){
                     this.pages++;
                     this.doc.addPage();
                     this.doc.rect(10,10,190,10,"F");
                     this.doc.setTextColor('#FFFFFF');
                     this.doc.setFont("Arial","bold");
                     this.doc.text("#",15,16);
                     var additionals = 24;
                        for(var ii=0;ii<this.keysTitle.length;ii++){
                            this.doc.text("| "+this.keysTitle[ii].title,additionals,16);
                            additionals = additionals+parseInt(this.keysTitle[ii].size);
                        }
                 }
                 
                 this.doc.setTextColor('#000000');
                 this.doc.setFont("Arial","normal");
                 var pageBack = 20+((this.pages-2)*25)+((this.pages-2)*2);
                 var addsx = 30+((x-pageBack)*10);
                 this.doc.text(""+(x+1),15,addsx);
                 var additionals = 24;
                 for(var ii=0;ii<this.keysTitle.length;ii++){
                    var textTo = eval("JS_AP.data[x]."+this.keysTitle[ii].index);
                    if(this.keysTitle[ii].date){
                        var Dats = new Date(textTo);
                        textTo = OrdinaryNumber(Dats.getDate())+" "+MONTHS[Dats.getMonth()]+" "+Dats.getFullYear();
                    }
                    textTo = "| "+textTo    
                        this.doc.text(textTo,additionals,addsx);
                        additionals = additionals+parseInt(this.keysTitle[ii].size);
                 }
                 
                 if(MorePagePointer==26){
                        MorePagePointer=0;   
                 } else MorePagePointer++;
             }
        }
        return this;
    }
    addLogo(fileUrl, FUN ,type="PNG"){
        loadImage(fileUrl).then((logo)=>{
            this.doc.addImage(logo,type,5,5,30,30);
            this.pointer.x = 30;
            this.pointer.y = 30;
            FUN(this);
        });   
        return this;
    }
    save(){
        this.doc.save(this.name);
    }
}

function loadImage( url){
        return new Promise((resolve) =>{
            let image = new Image();
            image.onload = () => resolve(image);
            image.src = url;
        })
}
    
function createHeaders(keys) {
        var result = [];
        for (var i = 0; i < keys.length; i += 1) {
            result.push({
            'id' : keys[i],
                'name': keys[i],
                'prompt': keys[i],
                'width': 65,
                'align': 'center',
                'padding': 0
            });
        }
        return result;
}