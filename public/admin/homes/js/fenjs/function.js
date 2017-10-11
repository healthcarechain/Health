/**
 * 公共函数
 */

/**
 * 添加cookie值
 */
function setCookie(name,value, expire)
{
	var Days = expire == null ? 30 : expire;
	var exp = new Date();
	exp.setTime(exp.getTime() + Days*24*60*60*1000);
	document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}

/**
 * 获取cookie值
 * @param name
 * @returns
 */
function getCookie(name)
{
	var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
	if(arr=document.cookie.match(reg))
		return unescape(arr[2]);
	else
		return null;
}


/**
 * 删除cookie
 * @param name
 */
function delCookie(name)
{
	var exp = new Date();
	exp.setTime(exp.getTime() - 1);
	var cval=getCookie(name);
	if(cval!=null)
		document.cookie= name + "="+cval+";expires="+exp.toGMTString();
}


/**
 * 新浪返回跳转链接
 * @param html
 */
function sina_redirect(url, title) {
	layer.open({
        type: 2,
        title: title,
        fix: true,
        shadeClose: true,
        maxmin: false,
        area: ['1000px', '545px'],
        content: url
    });
	
	
	/**
	var a = document.createElement("a");
    a.setAttribute("href", url);
    a.setAttribute("target", "_blank");
    a.setAttribute("id", "openwin");
    document.body.appendChild(a);
    a.click();
    return false;
    **/
}
