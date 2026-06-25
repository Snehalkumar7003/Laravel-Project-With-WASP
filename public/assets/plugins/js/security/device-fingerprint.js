class DeviceFingerprint {
    static generate() {
        const data = [
            navigator.userAgent,
            navigator.language,
            screen.width,
            screen.height,
            Intl.DateTimeFormat().resolvedOptions().timeZone,
            navigator.platform
        ].join('|');

        return btoa(data);
    }
}
