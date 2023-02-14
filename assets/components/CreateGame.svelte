<script>
    import * as bootstrap from 'bootstrap'
    import axios from 'axios'
    import BSTextModal from './BSTextModal.svelte'

    let name = ''
    let promise

    function createGame(evt) {
        //Call the create game endpoint, should return the game ID (join code)
        axios.post('/game/create', {
            name: evt.detail.value
        }).then((response => {
            if (response.status == 200) {
                window.location.href = "/game/" + response.data.code + "/lobby"
            }
        }))
    }

</script>

<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#gameNameModal">New Game</button>

<BSTextModal submitText="Create Game" id="gameNameModal" placeholder="Game Name" title="Create Game" on:submit={createGame}/>

<style>

</style>