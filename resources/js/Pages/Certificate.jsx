import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head} from '@inertiajs/react';

export default function Certificate({auth, src}) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Certificate</h2>}
        >
            <Head title="Certificate"/>

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            {/* Embed PDF using iframe */}
                            <iframe src={src} width="100%" height="600px"/>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
