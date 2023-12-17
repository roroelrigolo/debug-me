function hideNotification(number) {
    const id = "notification-"+number;
    const notification = document.getElementById(id);
    notification.style.display = "none";
}

const notificationButtons = document.getElementsByClassName("hideNotificationButton");
for(let i=0;i<notificationButtons.length;i++){
    let value = notificationButtons[i].value;
    notificationButtons[i].addEventListener("click", function() {
        hideNotification(value)
    });
}
