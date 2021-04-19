const actions = [
    { name: "login", triggers: "login log-in" },
    { name: "register", triggers: "signup register" }
]

const loggedinActions = [
    { name: "deposit", triggers: "deposit" },
    { name: "withdraw", triggers: "withdraw" },
    { name: "account-balance", triggers: "balance funds" },
    { name: "logout", triggers: "logout" }
]

const translateToAction = (text, loggedIn = false) => {
    let sanitizedText = text.toLocaleLowerCase().replace("-", "")
    let actionList = actions
    if (loggedIn) {
        actionList = actionList.concat(loggedinActions)
    }

    let action = actionList.filter(action => {
        let match = false
        action.triggers.split(" ").forEach(trigger => {
            if (sanitizedText.indexOf(trigger) !== -1) {
                match = true
            }
        })
        return match
    });

    return action
}

exports.translateToAction = translateToAction
