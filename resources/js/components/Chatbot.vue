<template>
    <div class="container">
        <div class="row">
            <div class="offset-md-3 col-md-6 border mt-5 rounded">
                <h3 class="text-center mt-3">PHP Challenge<br>Chatbot App</h3>
                <div id="chat-messages" class="border mt-3 p-3 rounded" style="height: 400px; overflow-x: auto">
                    <div style="margin-top: 300px">
                    <template v-for="message in messages">
                        <div v-bind:class="{ 'text-right' : message.from === 'user' }">
                            <div
                                :class="{
                                'alert p-0 d-inline' : true,
                                'alert-success': message.type === 'success',
                                'alert-danger' : message.type === 'error',
                                'alert-light' : message.type === 'light'}"
                                role="alert">
                                {{ message.text }}
                            </div>
                        </div>
                    </template>
                    <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="input-group mb-3">
                            <label class="sr-only" for="entry">Name</label>
                            <input :type="isTypingPassword ? 'password' : 'text'" class="form-control"
                                   id="entry"
                                   placeholder="" v-model="text" v-on:keyup.enter="send(text)">
                            <button id="send" @click="send(text)" class="btn btn-primary" type="button">Send
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
const default_layout = "default";
const SUCCESS = 'success';
const ERROR = 'error';
const PASSWORD_PLACEHOLER = "****************";
const translateToAction = require('./translateToAction').translateToAction
const util = require('./util')

export default {
    mounted() {
        axios.get('/sanctum/csrf-cookie').then()
        //axios.post('/api/logout').then()
        axios.get('/api/currency-code-list').then(response => this.currencyCodeList = response.data)
    },
    computed: {},
    data() {
        return {
            messages: [
                {
                    from: 'bot',
                    text: 'Hello! Welcome to your bank account',
                },
                {
                    from: 'bot',
                    text: 'How can I help you?',
                },
            ],
            currencyCodeList: [],
            text: null,
            nextAction: null,
            loading: false,
            isTypingPassword: false,
            username: null,
            defaultCurrency: null,
            email: null,
            password: null,
            password_confirmation: null,
            loggedIn: false,
            amount: null,
            currencyCode: null
        }
    },
    methods: {
        send: function (text) {
            var entry = text
            var action = null
            var actions = null
            this.text = null
            this.userMessage(entry)
            if (this.nextAction) {
                action = this.nextAction
            } else {
                this.text = null
                actions = translateToAction(entry);
                if (!actions.length) {
                    this.botMessage("Sorry, I did not undertand your request", ERROR)
                    return
                }
                if (actions.length > 1) {
                    this.botMessage("Sorry, I can only execute an action at a time", ERROR)
                    return
                }
                if (actions[0].mustBeLoggedin && !this.loggedIn) {
                    this.botMessage("You must be logged in to execute this action. If you don't have an account you can sign-up for a new one.", ERROR)
                    return
                }
                action = actions[0].name
            }

            switch (action) {
                case 'register':
                    this.botMessage('Enter your name')
                    this.nextAction = 'register-name'
                    break
                case 'register-name':
                    this.username = entry
                    this.botMessage('Enter the currency code you want to use in your account')
                    this.nextAction = 'register-default-currency'
                    break
                case 'register-default-currency':
                    this.defaultCurrency = entry
                    this.botMessage('Enter your e-mail')
                    this.nextAction = 'register-email'
                    break
                case 'register-email':
                    this.email = entry
                    this.botMessage('Enter your password')
                    this.isTypingPassword = true
                    this.nextAction = 'register-password'
                    break
                case 'register-password':
                    this.password = entry
                    this.botMessage('Enter your password confirmation')
                    this.isTypingPassword = true
                    this.nextAction = 'register-password-confirmation'
                    break
                case 'register-password-confirmation':
                    this.password_confirmation = entry
                    this.botMessage('Trying to register')
                    const registrationBody = {
                        name: this.username,
                        default_currency: this.defaultCurrency,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation
                    }
                    this.loading = true
                    axios.post('/api/register', registrationBody).then(response => {
                        this.botMessage('Signed up successfully! You can now log in.', SUCCESS)
                    }).catch(error => this.showErrors(error.response.data)).finally(() => this.loading = false)
                    this.nextAction = null
                    this.clearValues()
                    break
                case 'login':
                    this.botMessage('Enter your email')
                    this.nextAction = 'enter-password'
                    break
                case 'enter-password':
                    this.username = entry
                    this.botMessage('Please enter your password')
                    this.isTypingPassword = true
                    this.nextAction = 'try-login'
                    break
                case 'try-login':
                    this.isTypingPassword = false
                    this.nextAction = null
                    this.loading = true
                    axios.post('/api/login', {
                        email: this.username,
                        password: entry
                    }).then(response => {
                        this.botMessage('Login success!', SUCCESS)
                        this.loggedIn = true
                        this.clearValues()
                    }).catch(error => {
                        if (error.response) {
                            const {type, message} = error.response.data
                            this.botMessage(message, type)
                        } else {
                            this.botMessage('Somethign went wrong!', ERROR)
                        }
                    }).finally(() => this.loading = false)
                    break
                case 'deposit':
                    let extractedAmount = util.extractMoney(entry)
                    if (extractedAmount) {
                        this.amount = extractedAmount
                        this.currencyCode = util.extractCurrencyCode(entry, this.currencyCodeList)
                        this.sendDeposit()
                        this.clearValues()
                    } else {
                        this.botMessage('Enter the amount you want to deposit')
                        this.nextAction = 'deposit-ask-amount'
                    }
                    break
                case 'deposit-ask-amount':
                    this.amount = util.extractMoney(entry)
                    this.currencyCode = util.extractCurrencyCode(entry, this.currencyCodeList)
                    this.sendDeposit()
                    this.clearValues()
                    break
                case 'withdraw':
                    const withDrawAmount = util.extractMoney(entry)
                    if (withDrawAmount) {
                        this.amount = withDrawAmount
                        this.currencyCode = util.extractCurrencyCode(entry, this.currencyCodeList)
                        this.sendWithdraw()
                        this.clearValues()
                    } else {
                        this.botMessage('Enter the amount you want to withdraw')
                        this.nextAction = 'withdraw-ask-amount'
                    }
                    break
                case 'withdraw-ask-amount':
                    this.amount = util.extractMoney(entry)
                    this.currencyCode = util.extractCurrencyCode(entry, this.currencyCodeList)
                    this.sendWithdraw()
                    this.clearValues()
                    break
                case 'account-balance':
                    this.getAccountBalance()
                    break
                case 'logout':
                    this.loading = true
                    axios.post('/api/logout').then(response => {
                        this.botMessage('Successfully logged out!', SUCCESS)
                        this.loggedIn = false
                    }).finally(() => this.loading = false);
                    break
                case 'help':
                    if (this.loggedIn) {
                        this.botMessage('You are logged in. You can deposit, withdraw, and see your account balance')
                    } else {
                        this.botMessage('You are not logged in. You can log-in or sign-up')
                    }
                    break
                default:
                    this.botMessage('Invalid option', ERROR)
            }

            if (text === "register") {

            }
            this.text = "";
        },
        botMessage: function (text, type = '') {
            this.messages.push({
                from: 'bot', text, type
            })
            this.scroolChat()
        },
        userMessage: function (text) {
            if (this.isTypingPassword) {
                text = PASSWORD_PLACEHOLER
            }
            this.messages.push({
                from: 'user', text, type: 'light'
            })
            this.scroolChat()
        },
        scroolChat: function () {
            setTimeout(() => {
                let objet = this.$el.querySelector('#chat-messages')
                objet.scrollTop = objet.scrollHeight;
            }, 0)
        },
        sendDeposit: function () {
            this.loading = true
            axios.post('/api/transaction', {
                type: 'DEPOSIT',
                amount: this.amount,
                currency_code: this.currencyCode
            }).then(response => {
                this.botMessage('Deposit successful', SUCCESS)
                this.getAccountBalance()
            }).catch(error => {
                console.log(error.response.data)
                this.showErrors(error.response.data);
            }).finally(() => this.loading = false)
        },
        sendWithdraw: function () {
            this.loading = true
            axios.post('/api/transaction', {
                type: 'WITHDRAW',
                amount: this.amount,
                currency_code: this.currencyCode
            }).then(response => {
                this.botMessage('Withdraw successful', SUCCESS)
                this.getAccountBalance()
            }).catch(error => {
                this.showErrors(error.response.data);
                this.getAccountBalance()
            }).finally(() => this.loading = false)
        },
        showErrors: function (errorObject) {
            if (errorObject.message) {
                this.botMessage(errorObject.message, ERROR)
            }
            Object.values(errorObject.errors).flatMap(error => error).forEach(
                message => this.botMessage(message, ERROR)
            )
        },
        getAccountBalance: function() {
            axios.get('/api/account-balance').then(response => {
                const { data } = response
                this.botMessage('Your account balance is ' + data.account_balance + " " + data.default_currency, SUCCESS)
            }).catch(error => this.showErrors(error.response.data)).finally(() => this.loading = false)
        },
        clearValues: function () {
            this.username = null
            this.email = null
            this.password = null
            this.password_confirmation = null
            this.nextAction = null
            this.isTypingPassword = false
            this.defaultCurrency = null
            this.amount = null
            this.currencyCode = null
        },
    }
};
</script>
<style>
/* width */
::-webkit-scrollbar {
    width: 10px;
}

/* Track */
::-webkit-scrollbar-track {
    background: #f1f1f1;
}

/* Handle */
::-webkit-scrollbar-thumb {
    background: #c1bfbf;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
