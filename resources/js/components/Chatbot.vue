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
    },
    computed: {},
    data() {
        return {
            message:'Hello World'
        }
    }
};
</script>
