(function(doc){
    const theme_ = localStorage.getItem("forTheme");
    const body = doc.querySelector("body");
    
    const content = doc.querySelector('.content-wrapper-slc .content-panel');
  
    if(content){
        content.classList.remove('content-panel');
    }
    
    if(theme_){
        const cssDark_ = "https://bootswatch.com/5/darkly/bootstrap.min.css";
        const linkThema_ = doc.querySelector("#link-thema");
        switch(theme_){
            case "dark":
                    if(!linkThema_){
                        const head_ = doc.querySelector("head");
                        head_.innerHTML += `<link rel="stylesheet" href="${cssDark_}" id="link-theme">`;
                        if(!body.classList.contains('theme-dark')){
                            body.classList.add('theme-dark')
                        }
                    }
                break;
            default:
                if(linkThema_){
                    linkThema_.remove();
                    if(body.classList.contains('theme-dark')){
                        body.classList.remove('theme-dark')
                    }
                }
                if(body.classList.contains('theme-dark')){
                    body.classList.remove('theme-dark')
                }
        }
    }
})(document);;