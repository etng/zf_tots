Array.prototype.unique=function(){
    var ua=[];
    for(var i=0,m=this.length;i<m;i++)
    {
        if(!ua.exists(this[i]))
        {
            ua.push(this[i]);
        }
    }
    return ua;
};

Array.prototype.min=function(){
    return Math.min.apply(this, this);
};
Array.prototype.max=function(){
    return Math.max.apply(this, this);
};
Array.prototype.exists=function(x){
    for(var i=0,m=this.length;i<m;i++)
    {
        if(this[i]==x)
        {
            return true;
        }
    }
    return false;
};
Array.prototype.diff=function(other_array){
    var diff=[];
    for(var i=0,m=this.length;i<m;i++)
    {
        if(!other_array.exists(this[i]))
        {
            diff.push(this[i]);
        }
    }
    return diff;
};
Array.prototype.merge=function(other_array){
    for(var i=0,m=other_array.length;i<m;i++)
    {
        if(!this.exists(other_array[i]))
        {
            this.push(other_array[i]);
        }
    }
    return this;
};
Array.prototype.combine=function(other_array){
    if(this.length!=other_array.length)
    {
        return false;
    }
    var obj={};
    for(var i=0,m=other_array.length;i<m;i++)
    {
        obj[this[i]]=other_array[i];
    }
    return obj;
};
function range(a, b, step)
{
	step = step || 1;
	var range=[];
	if(typeof(a)!=typeof(b))
	{
		return false;
	}
	var char_mode=false;
	if(typeof(a)=='string')
	{
		if(a.length>1)
		{
			return false;
		}
		char_mode = true;
		a=a.charCodeAt(0);
		b=b.charCodeAt(0);		
	}

	if(a<b)
	{
		start=a;
		end=b;
	}
	else
	{
		start=b;
		end=a;
	}
	for(i=start;i<=end;)
	{
		range.push(char_mode?String.fromCharCode(i):i);
		i+=step;
	}
	return range;
}
function in_array(needle, haystack)
{
	return haystack.exists(needle);
}

window.STR_PAD_RIGHT=1;
window.STR_PAD_LEFT=2;
window.STR_PAD_BOTH=3;
str_pad = function(str, len, chr, pad_type)
{
	if((pad_type!=STR_PAD_RIGHT) && (pad_type!=STR_PAD_LEFT))
	{
		pad_type=STR_PAD_BOTH;
	}
	console.log(pad_type);
	str = String(str);
	var i=0;
	while(str.length<len)
	{
		
		if((pad_type==STR_PAD_RIGHT) ||((pad_type==STR_PAD_BOTH) && (i%2==0)))
		{
			str=str+String(chr);
		}
		else if((pad_type==STR_PAD_LEFT) ||((pad_type==STR_PAD_BOTH) && (i%2==1)))
		{
			str=String(chr)+str;
		}
		i++;
	}
	return str;
}
//console.log(str_pad('a', 3, 'x'))
//console.log(str_pad('a', 3, 'x', 1))
//console.log(str_pad('a', 3, 'x', 2))
//console.log(str_pad('a', 3, 'x', 3))
sprintf = function()
{
    var message_id=''; var values=[];
    for(var i=0,m=arguments.length;i<m;i++)
    {
        if(i==0)
        {
            message_id = arguments[i];
        }
        else
        {
            values.push(arguments[i]);
        }
    }

    var i=0;
    return message_id.replace(/%((\d+)\$)?(\d+)?(\.(\d+))?([dfs])/gi, function (str, p1, p2, p3, p4, p5, p6,offset, s)
	{
        var idx, v;
        if(p2!==undefined)
        {
            idx=parseInt(p2, 10)-1;
        }
        else
        {
            idx=i++;
        }
        if(values[idx]==undefined)
        {
            throw new RangeError('too few arguments');
        }
        v=values[idx];
        var p3=parseInt(p3, 10);
        var p5=parseInt(p5, 10);

        if(p3)
        {
            if((p6=='d' || p6=='f'))
            {
                var va=String(v).split('.', 2);
                while(String(va[0]).length<p3)
                {
                    va[0] = '0'+va[0];
                }
                if(p6=='f' && p5)
                {
                    if(va[1]==undefined)
                    {
                        va[1]=0;
                    }
                    while(String(v0).length<p3)
                    {
                        va[1] = '0'+v[1];
                    }
                }
                v=va.join('.');
            }
            else if((p6=='s'))
            {
                v = v.substr(0, p3);
            }
        }
        return v;
    });
};
strtr=function(strdict)
{
	return str.replace(/\{([^}]+)\}/gi, function (str, p1, offset, s)
	{
		console.log(str, p1, offset, s);
		var idx=p1;
		if((offset>0 && s[offset]=='\\') || (dict[idx]==undefined))
		{
			return str;
		}
		return dict[p1];
	});
};

str_wbr=function(str)
{
	return str.split('').join('&#8203');
};

str_repeat=function(str, n)
{
	var sa=[], i=0;
	while(i<n)
	{
		sa.push(str);
		i++;
	}
	return sa.join('');
};
//sprintf('%1$s: %2$2d+%3$2d=%4$2d, %4$2d-%2$2d=%3$2d', 'suan su', 2,3,5);
//strtr('{a}+{b}={c}  {d} can not be converted as \\{a\\}', {a:1,b:2,c:3});
//$('body').append($('<div style="word-break:break-all; word-wrap:break-word;width:300px;"></div>').html(str_repeat('a', 1000)));
//$('body').append($('<div style="word-break:break-all; word-wrap:break-word;width:300px;"></div>').html(str_wbr(str_repeat('a', 1000))));