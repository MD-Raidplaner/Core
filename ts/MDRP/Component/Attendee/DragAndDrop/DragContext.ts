export interface DragPayload {
    attendeeId: number;
    currentStatus: number;
    distribution: string;
    droppableTo: string;
    id: string;
}

export const DragContext = {
    current: null as DragPayload | null,
    clear(): void {
        this.current = null;
    },
    get(): DragPayload | null {
        return this.current;
    },
    set(payload: DragPayload): void {
        this.current = payload;
    },
}