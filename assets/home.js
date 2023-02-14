import Home from "./pages/Home.svelte";

let target = document.querySelector("#home")

const home = new Home({
    target: target,
});

export default home;