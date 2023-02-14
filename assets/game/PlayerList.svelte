<div class="player-list">
    <div class="text-center mb-4">{label}</div>
    <ul class="list-group">
        {#each players as p(p.id)}
            <GamePlayer bind:player={p} isself={player && p.id == player.id}/>
        {/each}
    </ul>
</div>

<script>
    import GamePlayer from '../components/GamePlayer.svelte'

    export let players
    export let player
    export let label = "Players"
</script>

<style>
    .player-list {
        height: 100%;
        background-color: #222;
    }
</style>