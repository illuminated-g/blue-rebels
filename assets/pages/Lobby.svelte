<div>
    <LobbyHero name={name} code={code} canStart={isCreator} bind:hasMinPlayers on:start={startGame}></LobbyHero>

    <PlayerName bind:player on:update={nameUpdate}/>

    <div class="container-lg">
        <div class="text-center">
            <p>Players:</p>
        </div>
        <ul class="list-group">
            {#each players as p(p.id)}
                <GamePlayer bind:player={p} isself={player && p.id == player.id} canKick={isCreator}/>
            {/each}
        </ul>
    </div>
</div>

<script>
    import LobbyHero from '../components/LobbyHero.svelte'
    import GamePlayer from '../components/GamePlayer.svelte'
    import PlayerName from '../components/PlayerName.svelte'

    import { onMount } from 'svelte'
    import axios from 'axios';

    let game

    let player = {
        id: 0,
        name: ''
    }

    let updateInterval

    $: name = game ? game.name : ''
    $: players = game ? game.players : []
    $: hasMinPlayers = players.length > 4
    $: isCreator = (game && player) ? game.creator.id == player.id : false

    //Will grab from global set from twig template
    let code = window.blueRebels.code

    function nameUpdate(data) {
        player.name = data.detail.name
    }

    function update() {
        //get game info
        axios.get(
            '/game/' + code + '/info'
        ).then((response) => {
            game = response.data.game

            if (game.started) {
                window.location.href = "/game/" + code
            }
            
            player.name = response.data.player.name
            player.id = response.data.player.id
        })
    }

    function startGame() {
        axios.post(
            '/game/' + code + '/start'
        ).catch(() => {
            alert('Unable to start game')
        })
    }

    onMount(() => {
        update()

        updateInterval = setInterval(update, 1000)
    })

</script>

<style>
    
</style>