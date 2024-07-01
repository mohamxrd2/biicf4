/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from "laravel-echo";
import Pusher from "pusher-js";
import Swal from "sweetalert2";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: "cdd50dab9a95edd6da7d",
    cluster: "eu",
    forceTLS: true,
});

// var channel = window.Echo.channel("my-channel"); // Use 'private-my-channel' for private channels
// channel.listen(".my-event", function (data) {
//     alert(JSON.stringify(data));
// });

// const userId = document
//     .querySelector('meta[name="user-id"]')
//     // .getAttribute("content");

// window.Echo.private(`App.Models.User.${userId}`).notification(
//     (notification) => {
//         console.log(notification);

//         // Display a SweetAlert2 toast notification
//         Swal.fire({
//             toast: true,
//             position: "top-end",
//             showConfirmButton: false,
//             timer: 3000,
//             icon: "success", // You can change this to 'success', 'error', 'warning', etc.
//             title: notification.message, // Customize this based on your notification structure
//         });
//     }
// );
