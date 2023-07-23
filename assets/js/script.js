const card = document.getElementById("card");
const type = document.getElementById("type");
const providerName = document.getElementById("provider-name");
const update = document.getElementById("update");
const connect = document.getElementById("connect");
const next = document.getElementById("next");

// change card style
let styleCard = (color) => {
    card.style.background = `radial-gradient(circle at 50% 0%, ${color} 36%, white 36%)`;
    card.querySelectorAll(".types span").forEach((item) => {
        item.style.backgroundColor = color;
    });
};
styleCard("#adcbe0");


fetch('feed.php?type=mtproto')
    .then(response => response.json())
    .then(init)
    .catch(error =>providerName.innerHTML = "Server Error");

let pointer = 0;
function init(data){
    if(data.ok === false)
    {
        providerName.innerHTML = "Server Error"
        return;
    }
    type.innerHTML = data.result[pointer].type;
    providerName.innerHTML = data.result[pointer].provider + " #"+data.result[pointer].priority;
    update.innerHTML = Math.ceil((data.response_time - data.result[pointer].time)/60) + " minute ago";
    connect.href = decodeURIComponent(data.result[pointer].data);

    next.addEventListener("click", () => {
        pointer++;
    if(pointer === data.result.length)
        pointer = 0;
    type.innerHTML = data.result[pointer].type;
    providerName.innerHTML = data.result[pointer].provider + " #"+data.result[pointer].priority;
    update.innerHTML = Math.ceil((data.response_time - data.result[pointer].time)/60) + " minute ago";
    connect.href = data.result[pointer].data;
    });
}