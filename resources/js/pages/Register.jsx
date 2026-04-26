import React, { useState } from 'react';
import axios from 'axios';
import { Link, useNavigate } from 'react-router-dom';

export default function Register() {
    const navigate = useNavigate();
    const [form, setForm] = useState({
        correo_usu: '',
        password_usu: '',
        password_usu_confirmation: '',
        nombres_per: '',
        ap_paterno_per: '',
        ap_materno_per: '',
    });
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(false);

    function update(field, value) {
        setForm((f) => ({ ...f, [field]: value }));
    }

    async function handleSubmit(e) {
        e.preventDefault();
        setError(null);
        setLoading(true);
        try {
            await axios.post('/auth/register', { ...form, rol_nombre: 'tutor' });
            navigate('/login');
        } catch (err) {
            const errs = err.response?.data?.errors;
            const msg = errs
                ? Object.values(errs).flat().join(' ')
                : err.response?.data?.message ?? 'No se pudo registrar.';
            setError(msg);
        } finally {
            setLoading(false);
        }
    }

    return (
        <div className="flex min-h-screen items-center justify-center px-4 py-10">
            <div className="w-full max-w-lg rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
                <h1 className="text-xl font-semibold text-slate-800">Registro (tutor)</h1>
                <p className="mt-1 text-sm text-slate-500">Crea una cuenta con rol tutor.</p>

                {error && (
                    <div className="mt-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
                        {error}
                    </div>
                )}

                <form className="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2" onSubmit={handleSubmit}>
                    <div className="sm:col-span-2">
                        <label className="block text-sm font-medium text-slate-700">Correo de acceso</label>
                        <input
                            type="email"
                            className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                            value={form.correo_usu}
                            onChange={(ev) => update('correo_usu', ev.target.value)}
                            required
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-slate-700">Contraseña</label>
                        <input
                            type="password"
                            className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                            value={form.password_usu}
                            onChange={(ev) => update('password_usu', ev.target.value)}
                            required
                            minLength={8}
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-slate-700">Confirmar</label>
                        <input
                            type="password"
                            className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                            value={form.password_usu_confirmation}
                            onChange={(ev) => update('password_usu_confirmation', ev.target.value)}
                            required
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-slate-700">Nombres</label>
                        <input
                            className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                            value={form.nombres_per}
                            onChange={(ev) => update('nombres_per', ev.target.value)}
                            required
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-slate-700">Ap. paterno</label>
                        <input
                            className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                            value={form.ap_paterno_per}
                            onChange={(ev) => update('ap_paterno_per', ev.target.value)}
                            required
                        />
                    </div>
                    <div className="sm:col-span-2">
                        <label className="block text-sm font-medium text-slate-700">Ap. materno</label>
                        <input
                            className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                            value={form.ap_materno_per}
                            onChange={(ev) => update('ap_materno_per', ev.target.value)}
                        />
                    </div>
                    <div className="sm:col-span-2">
                        <button
                            type="submit"
                            disabled={loading}
                            className="w-full rounded-md bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-60"
                        >
                            {loading ? 'Guardando…' : 'Registrarme'}
                        </button>
                    </div>
                </form>

                <p className="mt-6 text-center text-sm text-slate-500">
                    <Link className="font-medium text-indigo-600" to="/login">Volver al login</Link>
                </p>
            </div>
        </div>
    );
}
