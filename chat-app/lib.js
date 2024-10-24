// url is root path for server
// url is empty string for server else get from server
async function Hammer(url,channel,uid){
    var clientId = uid;
    var listener = ()=>{};
    if(clientId==null){
        const response = await fetch(url+'/gen_id.php');
        const json = await response.json();
        clientId = json.id;
    }

    function recv(f){
        listener = f;
    }

    function send(t, m){
        fetch(`${url}/send.php?c=${channel}`,{method: 'POST', body: JSON.stringify({f:clientId,t:t,m:m})});
    }

    function start(){
        fetch(`${url}/listen.php?c=${channel}&i=${clientId}`).then(m=>m.json()).then(m=>{
            listener(m);
            start();
        }).catch(e=>{
            start();
        });
    }

    function getId(){
        return clientId;
    }

    return {recv:recv,send:send,start:start,getId:getId};
};

/*
log = (name)=>function(x){
    console.log(name+" got ",x);
}

Hammer('http://localhost:8080','test-msg').then(h=>{
    h.recv(log('client'));
    setInterval(()=>h.send('','hello'),1000);
    h.start();
});

Hammer('http://localhost:8080','test-msg','').then(h2=>{
    h2.recv(log('server'));
    setInterval(()=>h2.send('*','hello2'),1000);
    h2.start();
});
*/