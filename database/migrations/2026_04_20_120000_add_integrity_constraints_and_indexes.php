<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared(<<<'SQL'
ALTER TABLE postulacion
    ADD CONSTRAINT uq_postulacion_est_oac UNIQUE (id_est_pos, id_oac_pos);

ALTER TABLE asignacion
    ADD CONSTRAINT uq_asignacion_pos UNIQUE (id_pos_asi);

ALTER TABLE cupo
    ADD CONSTRAINT uq_cupo_oac UNIQUE (id_oac_cup);

ALTER TABLE cupo
    ADD CONSTRAINT chk_cupo_total_nonneg CHECK (total_cup >= 0),
    ADD CONSTRAINT chk_cupo_disponibles_nonneg CHECK (disponibles_cup >= 0),
    ADD CONSTRAINT chk_cupo_disponibles_le_total CHECK (disponibles_cup <= total_cup);

ALTER TABLE procesamiento_ocr
    ADD CONSTRAINT chk_poc_confianza_rango
    CHECK (confianza_poc IS NULL OR (confianza_poc BETWEEN 0 AND 100));

CREATE INDEX idx_postulacion_est_oac_ept ON postulacion (id_est_pos, id_oac_pos, id_ept_pos);
CREATE INDEX idx_documento_pos ON documento (id_pos_doc);
CREATE INDEX idx_evaluacion_pos_cri ON evaluacion (id_pos_eva, id_cri_eva);
CREATE INDEX idx_asignacion_pos ON asignacion (id_pos_asi);
CREATE INDEX idx_lista_espera_oac ON lista_espera (id_oac_les);
CREATE INDEX idx_cupo_oac ON cupo (id_oac_cup);

CREATE OR REPLACE FUNCTION trg_validar_oferta_academica_coherencia()
RETURNS TRIGGER AS $$
DECLARE
    nivel_del_curso BIGINT;
BEGIN
    SELECT id_niv_cur
    INTO nivel_del_curso
    FROM curso
    WHERE id_cur = NEW.id_cur_oac;

    IF nivel_del_curso IS NULL THEN
        RAISE EXCEPTION 'No existe curso con id_cur=%', NEW.id_cur_oac;
    END IF;

    IF NEW.id_niv_oac <> nivel_del_curso THEN
        RAISE EXCEPTION
            'Inconsistencia de oferta_academica: id_niv_oac (%) debe coincidir con el nivel del curso (%)',
            NEW.id_niv_oac, nivel_del_curso;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_oferta_academica_validar_coherencia ON oferta_academica;
CREATE TRIGGER trg_oferta_academica_validar_coherencia
BEFORE INSERT OR UPDATE OF id_niv_oac, id_cur_oac
ON oferta_academica
FOR EACH ROW
EXECUTE FUNCTION trg_validar_oferta_academica_coherencia();
SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared(<<<'SQL'
DROP TRIGGER IF EXISTS trg_oferta_academica_validar_coherencia ON oferta_academica;
DROP FUNCTION IF EXISTS trg_validar_oferta_academica_coherencia();

DROP INDEX IF EXISTS idx_cupo_oac;
DROP INDEX IF EXISTS idx_lista_espera_oac;
DROP INDEX IF EXISTS idx_asignacion_pos;
DROP INDEX IF EXISTS idx_evaluacion_pos_cri;
DROP INDEX IF EXISTS idx_documento_pos;
DROP INDEX IF EXISTS idx_postulacion_est_oac_ept;

ALTER TABLE procesamiento_ocr
    DROP CONSTRAINT IF EXISTS chk_poc_confianza_rango;

ALTER TABLE cupo
    DROP CONSTRAINT IF EXISTS chk_cupo_disponibles_le_total,
    DROP CONSTRAINT IF EXISTS chk_cupo_disponibles_nonneg,
    DROP CONSTRAINT IF EXISTS chk_cupo_total_nonneg;

ALTER TABLE cupo
    DROP CONSTRAINT IF EXISTS uq_cupo_oac;

ALTER TABLE asignacion
    DROP CONSTRAINT IF EXISTS uq_asignacion_pos;

ALTER TABLE postulacion
    DROP CONSTRAINT IF EXISTS uq_postulacion_est_oac;
SQL);
    }
};
