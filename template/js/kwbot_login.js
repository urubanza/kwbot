$("#SUBMIT_LOGIN_ONE").submit( function(bn){
       let realLogin = document.createElement("input");
        $(realLogin).attr("type","text").attr("value","loginme").attr("name","login").hide();
        $(this).append(realLogin);
        
        
})  