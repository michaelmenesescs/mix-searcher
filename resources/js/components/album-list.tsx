import { Album } from "@/types";

export function AlbumList({ albums }: { albums: Album[] }) {
    return (
        <div>
            {albums.map((album) => (
                <div key={album.id}>{album.name}</div>
            ))}
        </div>
    );
}