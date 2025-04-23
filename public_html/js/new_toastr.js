(()=>{

  const toastAlerts = [
      {alert: "toast-info", background: "bg-info"},
      {alert: "toast-error", background: "bg-danger"},
      {alert: "toast-warning", background: "bg-warning"},
      {alert: "toast-primary", background: "bg-primary"},
      {alert: "toast-success", background: "bg-success"},
  ];
  
    
  toastAlerts.forEach(item => {
      const toast = document.querySelector('.'+item.alert);
      if(toast){
        if(!toast.classList.contains("d-none"))
            toast.classList.add('d-none');
        let title = toast.querySelector(".toast-title").innerHTML.trim();
        let message = toast.querySelector(".toast-message").innerHTML.trim();
        switch(item.alert){
            case "toast-info":
                info(title,message);
            break;
            case "toast-error":
                danger(title,message);
            break;
            case "toast-warning":
                warning(title,message);
            break;
            case "toast-success":
                success(title,message);
            break;
            default:
                info(title,message);
        }
      }
  })

})();;