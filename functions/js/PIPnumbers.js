function addCommasToNumber(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

function ExponentialValues(length,ValueTo){
    var exponentialvalues = new Array();
    for(var ii=0;length>Math.pow(ValueTo,ii);ii++){
        exponentialvalues.push(Math.pow(ValueTo,ii));
    }
    return exponentialvalues;
}

var get_exponentialvalues = function(indice){
       var expo = new Array();
       for(var ii=0;window.innerWidth>Math.pow(indice,ii);ii++){
        expo.push(Math.pow(indice,ii));
        } 
        return expo;
}

var get_natural_log_values = function(indice, length){
    var ln = new Array();
    for(var ii=0; ii<length; ii++){
        ln.push(Math.log(ii));
    }
}