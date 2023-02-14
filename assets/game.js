import Game from "./pages/Game.svelte";

let target = document.querySelector("#game")

const game = new Game({
    target: target,
});

export default game;