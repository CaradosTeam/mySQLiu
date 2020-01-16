class MysqliuConsole {
    constructor(report=true, defaultColor="#fff", backgroundColor="#000", defaultInputColor="#fff") {
        //General Output
        this.defaultColor = "#fff";
        this.backgroundColor = "#000";

        //Input
        this.defaultInputColor = "#fff";
        this.commandsHistory = [];

        let containerConsole = document.createElement("div"), inputConsole = document.createElement("div"), outputConsole = document.createElement("div"), inputConsoleCommand = document.createElement("div"),defaultStyle = document.createElement("style");

        defaultStyle.type = "text/css";
        defaultStyle.textContent = `
        ::selection {
            background: #fff;
            color: #000;
        }

        div#mysqliuConsole {
            /* display: inline-block; */
            width: 100%;
            height: 100%;
            /*position: fixed; padding-bottom: 60px;*/
            background: ${this.backgroundColor};
            color: ${this.defaultColor};
            
        }

        div#mysqliuConsole #mysqliuInput {
            color: ${this.defaultInputColor};
            padding: 10px;
            width: 100%;
            /*position: absolute;
            bottom: 0; left: 0;
            height: 40px;*/
        }

        span#mysqliuConsoleTyld {
            float: left;
            width: 150px;
        }

        div#mysqliuConsoleInputCommand {
            float: right;
            width: calc(100% - 170px);
            min-height: 20px;
            outline: none !important;
            display: inline-block;
            word-wrap: break-word;
        }

        div#mysqliuConsoleOutput {
            width: 100%;
            /* display: inline-block; */
            overflow-y: auto;
        }

        div#mysqliuConsole div#mysqliuConsoleOutput output {
            display: block;
            padding: 10px;
        }

        div#mysqliuConsoleOutput output.error {
            color: red;
        }
        `;

        containerConsole.id = "mysqliuConsole";
        inputConsole.id = "mysqliuConsoleInput";

        outputConsole.id = "mysqliuConsoleOutput";
        //containerConsole.appendChild(outputConsole);

        let commandTyld = document.createElement("span");
        commandTyld.id = "mysqliuConsoleTyld";
        commandTyld.innerHTML = '<span>mysqliu$username</span>&#x3E;';

        inputConsole.appendChild(commandTyld);
        inputConsoleCommand.id = "mysqliuConsoleInputCommand";
        inputConsoleCommand.setAttribute("contenteditable", "");
        inputConsole.appendChild(inputConsoleCommand);
        containerConsole.appendChild(inputConsole);

        //Console Node
        this.consoleNode = containerConsole;
        this.styleNode = defaultStyle;
    }

    addEvent(eventName) {
        return ({
            length: (()=>Object.keys(this).length-2()),
            names: (()=>Object.keys(this).filter(v=>v!="length" && v!="names")()),
            enter: ev=>{
                if(ev.keyCode === 13){
                    this.sendInput();
                }
            },
            arrowUp: ev=>{

            },
            arrowDown: ev=>{

            }
        }[eventName]);
    }

    addEvents(eventors) {
        function addEvents_fn(name) {
            switch(name) {
                case "enter":
                    document.getElementById("mysqliuConsole").onkeypress = this.addEvent("enter");
                break;
                case "arrowUp":
                    document.getElementById("mysqliuConsole").addEventListener("keyup",this.addEvent("arrowUp"));
                break;
                case "arrowDown":

                break;
            }
        }

        if(Array.isArray(eventors)) {
            for(let i = 0;i<eventors.length;i++) {
                addEvents_fn(eventors[i]);
            }
        } else {
            if(eventors=="*") {
                let evtNames = this.addEvents("names");
                for(let i = 0;i<this.addEvent("length");i++) {
                    addEvents_fn(evtNames[i]);
                }
            } else addEvents_fn(eventors);
        }
    }

    addAllEvents() {
        

    }

    printLine(text, type="normal") {
        if(document.getElementById("mysqliuConsoleOutput")!=null) {
            let outputRes = document.createElement("output");
            outputRes.innerHTML = text;
            switch(type) {
                case "error":
                    outputRes.className = "error";
                case "normal":
                default:
            }
            outputRes.id = "consoleOutput"+document.querySelectorAll("#mysqliuConsoleOutput output").length;
            document.getElementById("mysqliuConsoleOutput").apppendChild(outputRes);
        }
    }

    clear() {
        let consoleOutput = document.getElementById("mysqliuConsoleOutput");
        if(consoleOutput!=null) {
            while (consoleOutput.firstChild) consoleOutput.removeChild(consoleOutput.firstChild);
        }
    }

    sendInput() {
        try {
            let consoleInput = document.getElementById("mysqliuConsoleInputCommand");
            if(consoleInput!=null) {
               let cmdArgs = consoleInput.textContent.split(/\s*\"*\"+/).filter(v=>v!="");
               switch(cmdArgs[0]) {
                    case "-q":
                    if(cmdArgs.length==2) {

                    } else throw error("Required 2 arguments");
                    break;
               }
               this.commandsHistory.push(consoleInput.textContent);
               consoleInput.textContent = "";
            }
        } catch(erorr) {
            this.printLine(error, "error");
        }
    }

    injectTo(parentEl) {
        parentEl.appendChild(this.styleNode);
        parentEl.appendChild(this.consoleNode);
        return this;
    }
}

AsyncRequest = function(url,method="GET",data=undefined, params={}) {
    let pending = new WeakMap(), savedEvents = new WeakMap(), slf = this;
        this.url = url;
        params = Object.assign(params, {protocol:"http", port:"Default", type:"text", report:true});
        if(typeof params.type!="undefined") this.resType = params.type;
        if (window.XMLHttpRequest) {
            pending.set(this, {req:new XMLHttpRequest()});
         } else {
            pending.set(this, {req:new ActiveXObject("Microsoft.XMLHTTP")});
        }
        if(method.toUpperCase()=="GET" || method.toUpperCase()=="POST") this.method = method.toUpperCase(); else { if(typeof data!="undefined") this.method="POST"; else this.method="GET"; }
        if(typeof data!="undefined") this.data = data;


    this.postdataEncode = (objorarr=null)=>{
        if(typeof this.data=="object") { 
            let tempdata = ""; 
            for(let dataname in this.data) { 
                tempdata += dataname+"="+this.data[dataname]; 
                if(Object.keys(this.data)[Object.keys(this.data).length - 1]!=dataname) tempdata += "&"; 
            }
            return tempdata;
        } else return this.data;
    } 

    this.send = ()=>{
        return new Promise((resolve, reject)=>{
            console.log(pending.get(this).req);
            pending.get(this).req.open(this.method, this.url, true);
            pending.get(this).req.onreadystatechange = function() {
                if((this.readyState === XMLHttpRequest.DONE || this.readyState === 4) && this.status === 200) {
                    let restext = pending.get(slf).req.responseText, resp = pending.get(slf).req.response;
                    resolve(restext, resp, pending.get(slf).req.responseType);
                }
            }
            /*pending.get(this).req.onload = e=>{

            }*/
            pending.get(this).req.onerror = e=>{
                reject(e.target.status);
            }
            if(typeof this.resType!="undefined") pending.get(this).req.reponseType = this.resType;
            if(this.method=="POST") { pending.get(this).req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  console.log(this.postdataEncode()); pending.get(slf).req.send(this.postdataEncode()); } else pending.get(this).req.send();
        });
        
    }

    this.progress = (fn)=>{
        pending.get(this).req.onprogress = e=>{
            let percentComplete = (e.position / e.totalSize)*100;
            fn(percentComplete, e.totalSize, e.position);
        }
    }

    this.abort = ()=>{ return pending.get(this).req.abort(); }

    this.addEvent = (name, fn)=>{
        name = name.toLowerCase();
        let supportedEvents = ["loadstart", "loadend", "abort", "timeout", "load"];
        if(supportedEvents.includes(name)) {
            pending.get(this).req[name] = fn;
        } else {
            if(this.report) console.error(`Event with specifed name ${name} doesn't exists, use ${supportedEvents.join(",")} instead`);
        }
    }

    this.status = (arrorname=[])=>{
        let statusData = Object.create({}), expectedData = ["readyState", "timeout", "reponseURL", "status", "statusText"];
        if(Array.isArray(arrorname) && arrorname.length) arrorname = arrorname.filter(v=>expectedData.includes(v)); else arrorname = expectedData;
        /*for(let indx in pending.get(this).req) {
            if(expectedData.includes(indx)) Object.defineProperty(statusData, indx, {
                writable: false,
                value: pending.get(this).req[indx]
            });
        }*/
        arrorname.forEach(v=>{
            if(typeof pending.get(this).req[v]!="undefined") Object.defineProperty(statusData, v, {writable: false, value: pending.get(this).req[v]});
        });
        return statusData;
    }

    this.changeParams = (params={})=>{

    }
}

/*
let exampleQuery = new MysqliuQuery(["SELECT * FROM archive"]);
exampleQuery.prepare();
*/
class MysqliuQuery {
    constructor(queries) {
        this.queries = [];
        if(Array.isArray(queries)) this.queries = queries; else if(queries.match('/\"(.*);+\"/g').length>0) queries = queries.match('/\"(.*);+\"/g'); else ;
    }

    prepare() {
        return new Promise((resolve, reject)=>{
        let postdata = {};
        postdata["action"] = this.queries.length == 1 ? "query" : "multipleQuery";
        this.queries.forEach((v, i)=>{
            postdata["mysqliuQuery"+i] = v;
        });
        let req = new AsyncRequest(location.protocol+"//"+location.hostname+":"+location.port+"/server/mysqliuQueries.php","POST", postdata);
        //console.log(location.protocol+"//"+location.hostname+"/server/mysqliuQueries.php");
        req.send().then((restxt, res)=>{
            console.log(restxt, res);
            resolve(restxt, res);
        }).catch((e)=>{
            console.log(e);
            reject(e);
        });
        });
    }
}

class PushNotification {
    
}
