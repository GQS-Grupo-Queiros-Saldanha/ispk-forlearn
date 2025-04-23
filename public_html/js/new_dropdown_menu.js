((doc, win) => {

  const menus = doc.querySelectorAll(".menu-item");

  const menuClose = (menu) => {
    if(menu.classList.contains("open")){
      menu.classList.remove("open")
    }

    if(!menu.classList.contains("hidden-submenu")){
      menu.classList.add("hidden-submenu")
    }
  }

  const closeSubmenuOpen = (item) => {
    const menus = doc.querySelectorAll(".menu-item.open");
    menus.forEach((menu) => {
      if (item != menu) {
        menuClose(menu);
      }
    });
  };
  
  const openMenuAndCloseAll = (menu) => {
    const menus = doc.querySelectorAll(".menu-item.dropdown-sub");
        menus.forEach((item) => {
        if (item != menu) {
            menuClose(item);
        }
    });
  };

  const closeMenuAll = () => {
    const menus = doc.querySelectorAll(".menu-item.dropdown-sub");
    menus.forEach((menu) => {
      if (menu) {
         menuClose(menu);
      }
    });
  };

  const operationInMenu = (menu)=>{
    if (menu.classList.contains("dropdown-sub")) {
      const submenu = menu.querySelector(".dropdown-submenu");
      if (menu.classList.contains("hidden-submenu")) {
        menu.classList.remove("hidden-submenu");
        const submenuRect = submenu.getBoundingClientRect();
        const windowWidth = win.innerWidth;

        if (submenuRect.right > windowWidth) {
            submenu.style.right = "0.2rem";
        }

        if(!menu.classList.contains("open")){
          menu.classList.add("open");
          closeSubmenuOpen(menu)
        }else{
          closeMenuAll();
        }
      }else{
         if(!menu.classList.contains("open")){
            menu.classList.add("open");
         }
        openMenuAndCloseAll(menu);
      } 
    }else{
      closeMenuAll();
    }
}

  menus.forEach((menu) => {
      
    menu.addEventListener("mouseover", (e) => {
      operationInMenu(menu);
    });
    
    menu.addEventListener("mouseout", (e) => {
        if (menu.classList.contains("dropdown-sub","open")) {
            if(!menu.classList.contains('perm')){
                menuClose(menu);
            }
        }
    });
    
  });
  
  win.addEventListener('click',(e)=>{
    const menus = doc.querySelectorAll(".menu-item.open");
    menus.forEach((menu) => {
        menuClose(menu);
    });
  })


})(document, window);;