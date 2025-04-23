((doc) => {
  
  const menuLinks = doc.querySelectorAll('.menu-link');
  
  function capitalize(str){
      return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
  }
  
  menuLinks.forEach(item => {
      let span = item.querySelector('span');
      if(span){
          span.innerHTML = capitalize(span.innerHTML.trim())
      }
  })

  try{
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
  }catch(e){
      
  }
  
  const content = doc.querySelector('.content-wrapper-slc .content-panel');
  
  if(content){
        content.classList.remove('content-panel');
  }

  const THEME_ATTRIBUTE = "theme";
  const ID_LINK_THEME = "link-theme";
  const VAR_STORAGE = "forTheme";

  const btnMode = doc.querySelector("#btn-mode");

  if(localStorage.getItem(VAR_STORAGE)){
    iconChengeTheme(localStorage.getItem(VAR_STORAGE));
  }
  
  function createLinkTheme(css) {
    const head = doc.querySelector("head");
    head.innerHTML += `<link rel="stylesheet" href="${css}" id="${ID_LINK_THEME}">`;
  }
  
  function iconChengeTheme(theme) {
    const icon = doc.querySelector("#icon-theme");
    const body = doc.querySelector("body");
    if (icon) {
      switch (theme) {
        case "dark":
            if (icon.classList.contains("fa-moon")) {
                icon.classList.remove("fa-moon");
                icon.classList.add("fa-sun");
            }
            if(!body.classList.contains("theme-dark")){
               body.classList.add("theme-dark");
            }            
            btnMode.setAttribute("data-bs-title","Modo Padrão");
          break;
        default:
            if (icon.classList.contains("fa-sun")) {
                icon.classList.remove("fa-sun");
                icon.classList.add("fa-moon");
            }
            if(body.classList.contains("theme-dark")){
              body.classList.remove("theme-dark");
            }
            btnMode.setAttribute("data-bs-title","Modo Dark");
      }
    }
  }

  function changeTheme(theme) {
    const cssDark = "https://bootswatch.com/5/darkly/bootstrap.min.css";
    const linkThema = doc.querySelector(`#${ID_LINK_THEME}`);
    switch (theme) {
      case "dark":
        if (!linkThema) {
          createLinkTheme(cssDark);
        } else {
          linkThema.href = cssDark;
        }
        btnMode.setAttribute(THEME_ATTRIBUTE, "dark");
        localStorage.setItem(VAR_STORAGE, "dark");
        break;
      default:
        if (linkThema) {
          linkThema.remove();
        }
        btnMode.setAttribute(THEME_ATTRIBUTE, "default");
        localStorage.setItem(VAR_STORAGE, "default");
    }
    iconChengeTheme(theme);
  }

  btnMode.addEventListener("click", function (e) {
    const theme = localStorage.getItem(VAR_STORAGE);
    if (theme) {
      changeTheme(theme == "dark" ? "default" : "dark");
    } else {
      const th = btnMode.getAttribute(THEME_ATTRIBUTE);
      localStorage.setItem("forTheme", th);
      changeTheme(th);
    }
  });

})(document);
;