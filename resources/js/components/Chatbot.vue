<template>
    <div>
        {{message}}
    </div>
</template>
<script>
const default_layout = "default";

export default {
    mounted() {
        console.log('Component mounted.')
        axios.get('/api/test').then(response => console.log(response))
        axios.get('/api/test-auth').then(response => console.log(response))
        /* axios.post('/api/register', {
            name: "André",
            email: "andrealoisio+2@gmail.com",
            password: "senhateste123",
            password_confirmation: "senhateste123"
        }).then(response => console.log(response)) */
        axios.get('/sanctum/csrf-cookie').then(response => {
            // console.log(response)
            axios.post('/api/login', {
                email: 'andrealoisio@gmail.com',
                password: 'senhateste123'
            }).then(response => {
                console.log(response);
                axios.get('/api/test-auth').then(response => console.log(response))
            })
        });
    },
    computed: {},
    data() {
        return {
            message:'Hello World'
        }
    }
};
</script>
