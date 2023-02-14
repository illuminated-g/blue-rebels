<div class="container-sm my-3">
    <form on:submit|preventDefault={joinGame}>
        <div class="input-group">
            <input type="text" class="form-control" placeholder = "Join Code" bind:value={code}>
            <button class="btn btn-primary" type="submit">Join</button>
        </div>
    </form>
</div>

<script>
    import axios from "axios";



    let code = '';

    function joinGame() {
        if (code != '') {
            axios.post(
                '/game/' + code + '/join'
            ).then(() => {
                window.location.href = "/game/" + code + "/lobby"
            })
        }
    }

</script>

<style>
</style>