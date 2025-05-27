import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';


export default function Artists() {
    return (
        <AppLayout>
            <Head title="Artists" />
        </AppLayout>
    );
}