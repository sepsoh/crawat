const card = document.getElementById("card");
const types = document.getElementById("types");
const providerName = document.getElementById("provider-name");
const providerNameSub = document.getElementById("provider-name-sub");
const update = document.getElementById("update");
let connect = document.getElementById("connect");
const next = document.getElementById("next");
const image = document.getElementById("image");

let mtproto_pointer = 0;
let ss_pointer = 0;
let vmess_pointer = 0;
let vless_pointer = 0;

let mtproto_data = [];
let ss_data = [];
let vmess_data = [];
let vless_data = [];






let styleCard = (color) => {
    card.style.background = `radial-gradient(circle at 50% 0%, ${color} 36%, white 36%)`;
    card.querySelectorAll(".types span").forEach((item) => {
        item.style.backgroundColor = color;
    });
};



function mtproto(){
    providerName.innerHTML = "Connecting ...";
    image.src = "assets/img/mtproto.png";
    if(mtproto_data.length === 0) {
        fetch('feed.php?type=mtproto')
            .then(response => response.json())
            .then(data => {
                mtproto_data = data
                mtproto_flush(mtproto_data.result[mtproto_pointer]);

                next.addEventListener("click", () => {
                    if (types.value === "mtproto") {
                        mtproto_pointer++;
                        if (mtproto_pointer === mtproto_data.result.length)
                            mtproto_pointer = 0;
                        mtproto_flush(mtproto_data.result[mtproto_pointer]);
                    } else if (types.value === "ss") {
                        ss_pointer++;
                        if (ss_pointer === ss_data.result.length)
                            ss_pointer = 0;
                        ss_flush(ss_data.result[ss_pointer]);
                    }else if (types.value === "vmess") {
                        vmess_pointer++;
                        if (vmess_pointer === vmess_data.result.length)
                            vmess_pointer = 0;
                        vmess_flush(vmess_data.result[vmess_pointer]);
                    }else if (types.value === "vless") {
                        vless_pointer++;
                        if (vless_pointer === vless_data.result.length)
                            vless_pointer = 0;
                        vless_flush(vless_data.result[vless_pointer]);
                    }
                });


            });
    }else{
        mtproto_flush(mtproto_data.result[mtproto_pointer]);
    }





    styleCard("#adcbe0");

}

function mtproto_flush(item){
    providerName.innerHTML = item.provider + " #"+item.priority;
    providerNameSub.innerHTML = '';
    update.innerHTML = Math.ceil((mtproto_data.response_time - item.time)/60) + " minutes ago";

    // remove all connect event listener
    const new_connect = connect.cloneNode(true);
    connect.parentNode.replaceChild(new_connect, connect);
    connect = new_connect;

    connect.innerHTML = "Connect";
    connect.addEventListener("click",()=>{
        window.open(item.data);
    });
}


function ss(){
    providerName.innerHTML = "Connecting ...";
    image.src = "assets/img/ss.png";
    styleCard("rgba(103,177,104,0.37)");

    if(ss_data.length === 0) {
        fetch('feed.php?type=ss')
            .then(response => response.json())
            .then(data => {
                ss_data = data
                ss_flush(ss_data.result[ss_pointer]);
            });
    }else{
        ss_flush(ss_data.result[ss_pointer]);
    }

}
function ss_flush(item){
    providerName.innerHTML = item.provider + " #"+item.priority;
    providerNameSub.innerHTML = item.tag;
    update.innerHTML = Math.ceil((ss_data.response_time - item.time)/60) + " minutes ago";

    // remove all connect event listener
    const new_connect = connect.cloneNode(true);
    connect.parentNode.replaceChild(new_connect, connect);
    connect = new_connect;

    connect.innerHTML = "Copy";
    connect.addEventListener("click",()=>{
        navigator.clipboard.writeText(item.data);
        connect.innerHTML = "Copied";
        setTimeout(()=>{
            connect.innerHTML = "Copy";
        },2000);

    });
}

function vmess(){
    providerName.innerHTML = "Connecting ...";
    image.src = "assets/img/vmess.png";
    styleCard("rgba(84,100,136,0.37)");

    if(vmess_data.length === 0) {
        fetch('feed.php?type=vmess')
            .then(response => response.json())
            .then(data => {
                vmess_data = data
                vmess_flush(vmess_data.result[vmess_pointer]);
            });
    }else{
        vmess_flush(vmess_data.result[vmess_pointer]);
    }

}
function vmess_flush(item){
    providerName.innerHTML = item.provider + " #"+item.priority;
    providerNameSub.innerHTML = item.tag;
    update.innerHTML = Math.ceil((vmess_data.response_time - item.time)/60) + " minutes ago";

    // remove all connect event listener
    const new_connect = connect.cloneNode(true);
    connect.parentNode.replaceChild(new_connect, connect);
    connect = new_connect;

    connect.innerHTML = "Copy";
    connect.addEventListener("click",()=>{
        navigator.clipboard.writeText(item.data);
        connect.innerHTML = "Copied";
        setTimeout(()=>{
            connect.innerHTML = "Copy";
        },2000);

    });
}

function vless(){
    providerName.innerHTML = "Connecting ...";
    image.src = "assets/img/vless.png";
    styleCard("rgba(136,84,121,0.37)");

    if(vless_data.length === 0) {
        fetch('feed.php?type=vless')
            .then(response => response.json())
            .then(data => {
                vless_data = data
                vless_flush(vless_data.result[vless_pointer]);
            });
    }else{
        vless_flush(vless_data.result[vless_pointer]);
    }

}
function vless_flush(item){
    providerName.innerHTML = item.provider + " #"+item.priority;
    providerNameSub.innerHTML = item.tag;
    update.innerHTML = Math.ceil((vless_data.response_time - item.time)/60) + " minutes ago";

    // remove all connect event listener
    const new_connect = connect.cloneNode(true);
    connect.parentNode.replaceChild(new_connect, connect);
    connect = new_connect;

    connect.innerHTML = "Copy";
    connect.addEventListener("click",()=>{
        navigator.clipboard.writeText(item.data);
        connect.innerHTML = "Copied";
        setTimeout(()=>{
            connect.innerHTML = "Copy";
        },2000);

    });
}


fetch('feed.php?list')
    .then(response => response.json())
    .then(data => {
        const typeList = {
            "mtproto": "MTProto",
            "ss": "Shadowsocks",
            "vmess" : "Vmess",
            "vless" : "Vless"
        }
        data.result.forEach(type => {
            const opt =  document.createElement('option');
            opt.value = type;
            opt.text = typeList[type];
            types.add(opt);
        });
        //first call
        mtproto();

        types.addEventListener("change", () => {
            if(types.value === "mtproto")
                mtproto();
            else if(types.value === "ss")
                ss();
            else if (types.value === "vmess")
                vmess();
            else if (types.value === "vless")
                vless();


        });
    });




