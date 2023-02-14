
<li class="list-group-item">
    {#if canKick && !isself}
        <button class="btn btn-sm btn-danger" on:click={kickPlayer}>Kick</button>
    {/if}
    <span class:self={isself}>{playerName}</span>
</li>

<script>
    import axios from "axios";

    export let player
    export let canKick = false
    export let isself = false

    $: playerName = player && player.name ? player.name : 'Unknown Name (' + player.id + ')'

    function kickPlayer() {
        if (player) {
            axios.post(
                '/game/kick',
                {
                    player_id: player.id
                }
            ).then(() => {
                alert("Player kicked")
            })
        }
    }
</script>

<style>
    .self {
        color: cornflowerblue;
    }
</style>