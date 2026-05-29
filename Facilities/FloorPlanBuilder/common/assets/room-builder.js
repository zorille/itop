window.LOCATION_BUILDER_WS ??= '';

window.LocationBuilder = {
    oldTabText: null,

    /**
     * @param {string} selector
     * @param {string} tabText
     * @returns {Promise<void>}
     */
    async mount(selector, tabText) {
        const el = document.querySelector(selector);
        if (!el) return;
        
        el?.setAttribute('lang-keys', JSON.stringify(RoomBuilder.tradKeys))

        const locationId = el.getAttribute('room-id');
        const baseUrl = window.LOCATION_BUILDER_WS;

        const url = `${baseUrl}&op=locations&location_id=${locationId}`;

        el.addEventListener(
            'saved',
            /**
             * @param {CustomEvent<[{roomName: string, layers: any[]}]>} e
             */
            async (e) => {
                const [object] = e.detail;
                console.log(object)

                try {
                    await CombodoHTTP.Fetch(url, {
                        method: 'PUT',
                        header: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            roomName: object.roomName,
                            layers: object.layers
                        })
                    })

                    RoomBuilder.success({
                        title: 'Succès',
                        text: 'La salle a été enregistrée avec succès',
                    })
                } catch (err) {
                    RoomBuilder.error({
                        title: 'Erreur',
                        text: 'Une erreur est survenue lors de l\'enregistrement',
                    })
                }
            })

        el.addEventListener('refresh', async () => {
            el?.setAttribute('is-data-loading', 'true');
            await load();
            el?.removeAttribute('is-data-loading');
        })

        const load = async () => {
            try {
                const res = await CombodoHTTP.Fetch(url, {
                    method: 'GET',
                    headers: {'Accept': 'application/json'},
                });

                /**
                 * @type {{data: {roomId: string, roomName: string, layers: any[]}}}
                 */
                const json = await res.json().catch(() => null);

                if (!res.ok) {
                    console.log({ http: res.status, body: json });
                    return;
                }
                console.log(json)
                el.setAttribute('layers', JSON.stringify(json.data.layers));
            } catch (e) {
                console.log({ ok: false, error: String(e) })
            } finally {
                el?.removeAttribute('is-data-loading');
            }
        };
        await load();

        [...document.querySelectorAll('ul > li > a')].map((a, i) => {
            if (i === 0) this.oldTabText = a.innerText;

            a.addEventListener('click', () => {
                const tab = a.getAttribute('href').substring(1);
                const url = new URL(window.location.href);
                url.searchParams.set('ObjectProperties', tab);
                history.pushState(null, '', url.toJSON());

                if (this.oldTabText !== a.innerText) {
                    if (a.innerText === tabText) load();
                    this.oldTabText = a.innerText;
                }
            })
        });
    }
};