//Add all your global function over here

export function formatDate(value) {
    if (value) {
        const date = new Date(value);
        return date.toLocaleString();
    }
}

export function getHoursMinutes(seconds, short = true) {
    if (seconds == 0) return "0";

    let hours = Math.floor(seconds / 3600);
    let minutes = Math.floor((seconds / 60) % 60);
    seconds = seconds % 60;

    let result = '';
    if (hours > 0) {
        result += hours + ' hours ';
        if (short) return result;
    }
    if (minutes > 0) {
        result += minutes + ' minutes ';
        if (short) return result;
    }
    if (seconds > 0) {
        result += seconds + ' seconds';
        if (short) return result;
    }

    return result;
}