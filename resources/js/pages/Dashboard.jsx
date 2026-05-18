import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';

const ROLE_LABELS = {
    admin_general: 'Administrador general',
    admin_institucional: 'Administrador institucional',
    tutor: 'Tutor',
};

function roleLabel(nombreRol) {
    if (!nombreRol) return '—';
    return ROLE_LABELS[nombreRol] ?? nombreRol.replaceAll('_', ' ');
}

export default function Dashboard() {
    const navigate = useNavigate();
    const [user, setUser] = useState(null);
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        let cancelled = false;
        (async () => {
            try {
                const { data } = await axios.get('/auth/me');
                if (!cancelled) {
                    setUser(data.data);
                }
            } catch (e) {
                if (!cancelled) {
                    setError('No se pudo cargar el perfil.');
                }
            } finally {
                if (!cancelled) {
                    setLoading(false);
                }
            }
        })();

        return () => {
            cancelled = true;
        };
    }, []);

    async function logout() {
        try {
            await axios.post('/auth/logout');
        } catch {
            /* ignorar */
        }
        localStorage.removeItem('auth_token');
        navigate('/login');
    }

    if (loading) {
        return (
            <div className="flex min-h-screen items-center justify-center text-slate-600">
                Cargando…
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-slate-50">
            <header className="border-b border-slate-200 bg-white">
                <div className="mx-auto flex max-w-5xl items-center justify-between px-4 py-4">
                    <h1 className="text-lg font-semibold text-slate-800">Panel</h1>
                    <button
                        type="button"
                        onClick={logout}
                        className="rounded-md border border-slate-300 px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50"
                    >
                        Cerrar sesión
                    </button>
                </div>
            </header>
            <main className="mx-auto max-w-5xl px-4 py-8">
                {error && (
                    <div className="mb-4 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900">
                        {error}
                    </div>
                )}
                {user && (
                    <div className="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 className="text-base font-medium text-slate-800">Usuario autenticado</h2>
                        <dl className="mt-4 grid gap-3 text-sm text-slate-600">
                            <div>
                                <dt className="font-medium text-slate-700">Correo</dt>
                                <dd>{user.correo_usu}</dd>
                            </div>
                            <div>
                                <dt className="font-medium text-slate-700">Rol</dt>
                                <dd>{roleLabel(user.rol?.nombre_rol)}</dd>
                            </div>
                            <div>
                                <dt className="font-medium text-slate-700">Nombre</dt>
                                <dd>
                                    {[user.persona?.nombres_per, user.persona?.ap_paterno_per, user.persona?.ap_materno_per]
                                        .filter(Boolean)
                                        .join(' ')}
                                </dd>
                            </div>
                        </dl>
                        <p className="mt-6 text-sm text-slate-500">
                            Desde aquí puedes continuar con pantallas de postulaciones, cupos y evaluación multicriterio.
                        </p>
                    </div>
                )}
            </main>
        </div>
    );
}
