<div class="row h-75">
    <div class="col-10 h-100">
        <GameArea bind:game bind:state bind:player bind:area bind:leader bind:isbad/>
    </div>
    <div class="col-2 h-100">
        <PlayerList bind:players bind:player label="All Players"/>
    </div>
</div>

<script>
    import axios from 'axios';
    import { onMount } from 'svelte'

    import GameArea from '../game/GameArea.svelte'
    import PlayerList from '../game/PlayerList.svelte'

    let code = window.blueRebels.code
    let game
    let player
    let state
    let area
    let updateTimeout
    let players = []
    let leader = ""
    let isbad = false;

    $: leader = (game && state)? leader = game.players[state.leaderIndex].name : ""

    //once a second get game state from the server
    function update() {
        //area returns what the current state of the game area should be.
        axios.get(
            '/game/' + code + '/state'
        ).then((response) => {
            state = response.data.state
            area = response.data.area
        })

        updateTimeout = setTimeout(update, 1000);
    }

    onMount(() => {
        axios.get(
            '/game/' + code + '/info'
        ).then((response) => {
            game = response.data.game
            players = game.players
            player = response.data.player

            //kickoff periodic state updates
            update()
        })

        axios.get(
            '/game/' + code + '/amibad'
        ).then((response) => {
            isbad = response.data.isBad
        })
    })

</script>

<style>
</style>