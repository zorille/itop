declare global {
    let RoomBuilder: {
        success: (options: { title: string, text: string, autoclose?: boolean, autotimeout?: number }) => void,
        error: (options: { title: string, text: string, autoclose?: boolean, autotimeout?: number }) => void,
        tradKeys: Record<string, string>
    };

    interface RoomBuilderElement extends HTMLElement {
        roomId: number;
        roomName: string;
        layers?: string;
        radius?: number
        disableAddRacks?: boolean
        useItopForm?: boolean
        itopCreateRackUrl?: string,
        isDataLoading?: boolean,
        language?: string
    }

    interface HTMLElementTagNameMap {
        "room-builder": RoomBuilderElement;
    }
}

export {}