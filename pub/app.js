"use strict";

document.addEventListener("DOMContentLoaded", async () => {
    await navigator.serviceWorker.register("./ServiceWorker.js");
});