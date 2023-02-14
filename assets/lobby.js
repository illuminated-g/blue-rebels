import Lobby from "./pages/Lobby.svelte";

let target = document.querySelector("#lobby")

const lobby = new Lobby({
    target: target,
});

export default lobby;